<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Certificat;
use App\Models\InscriptionFormation;
use App\Models\NiveauFormation;
use App\Models\Notification;
use App\Models\Qcm;
use App\Models\SessionQcm;
use Illuminate\Http\Request;

class QcmController extends Controller
{
    // ===== LISTE QCMs AVEC HISTORIQUE =====
    public function index()
    {
        $user = auth()->user();

        $formationIds = InscriptionFormation::where('user_id', $user->id)
            ->where('statut', 'valide')
            ->pluck('formation_id');

        $qcms = Qcm::whereIn('formation_id', $formationIds)
            ->where('actif', true)
            ->with(['formation', 'niveau'])
            ->withCount('questions')
            ->get()
            ->map(function ($qcm) use ($user) {
                $sessions = SessionQcm::where('qcm_id', $qcm->id)
                    ->where('user_id', $user->id)
                    ->orderByDesc('tentative')
                    ->get();

                $qcm->mes_sessions       = $sessions;
                $qcm->tentatives_faites  = $sessions->count();
                $qcm->meilleure_note     = $sessions->max('note');
                $qcm->derniere_note      = $sessions->first()?->note;
                $qcm->derniere_tentative = $sessions->first()?->created_at;

                $qcm->deja_reussi   = $sessions->where('reussi', true)->count() > 0;
                $qcm->peut_repasser = $sessions->count() < $qcm->tentatives_max
                                      && !$qcm->deja_reussi;

                // Un QCM de niveau ne donne jamais de certificat.
                // Seul le QCM "formation entière" (niveau_id = null) en génère un,
                // et uniquement si la formation est payante.
                if ($qcm->deja_reussi && $qcm->niveau_id === null) {
                    $sessionReussie = $sessions->where('reussi', true)->first();
                    $qcm->certificat = Certificat::where('session_qcm_id', $sessionReussie?->id)
                        ->where('user_id', $user->id)
                        ->first();
                } else {
                    $qcm->certificat = null;
                }

                // Indicateur de verrouillage pour affichage dans la vue
                $qcm->est_verrouille = !$this->qcmEstAccessible($qcm, $user->id);

                return $qcm;
            });

        $stats = [
            'total' => $qcms->count(),
            'reussis' => $qcms->filter(fn($q) => $q->deja_reussi)->count(),
            'en_cours' => $qcms->filter(fn($q) => !$q->deja_reussi && $q->tentatives_faites > 0)->count(),
            'non_tentes' => $qcms->filter(fn($q) => $q->tentatives_faites == 0)->count(),
        ];

        return view('client.qcms.index', compact('qcms', 'stats'));
    }

    // ===== DÉMARRER LE QCM =====
    public function demarrer(Qcm $qcm)
    {
        $user = auth()->user();

        $inscrit = InscriptionFormation::where('user_id', $user->id)
            ->where('formation_id', $qcm->formation_id)
            ->where('statut', 'valide')
            ->exists();

        abort_if(!$inscrit, 403, 'Vous n\'êtes pas inscrit à cette formation.');
        abort_if(!$qcm->actif, 403, 'Ce QCM n\'est pas disponible.');

        // Vérifier la progression : niveau précédent validé, ou tous les
        // niveaux validés s'il s'agit du QCM final de la formation.
        if (!$this->qcmEstAccessible($qcm, $user->id)) {
            return redirect()->route('client.qcms.index')
                ->with('error', $this->messageVerrouillage($qcm));
        }

        $tentativesFaites = SessionQcm::where('qcm_id', $qcm->id)
            ->where('user_id', $user->id)
            ->count();

        $dejaReussi = SessionQcm::where('qcm_id', $qcm->id)
            ->where('user_id', $user->id)
            ->where('reussi', true)
            ->exists();

        if ($dejaReussi) {
            $message = $qcm->niveau_id
                ? '🎉 Vous avez déjà validé ce niveau !'
                : '🎉 Vous avez déjà réussi ce QCM final !';

            return redirect()->route('client.qcms.index')->with('info', $message);
        }

        if ($tentativesFaites >= $qcm->tentatives_max) {
            return redirect()->route('client.qcms.index')
                ->with('error', "❌ Vous avez épuisé vos {$qcm->tentatives_max} tentatives.");
        }

        $qcm->load(['questions' => function ($q) {
            $q->orderBy('ordre')->with(['reponses' => function ($r) {
                $r->inRandomOrder();
            }]);
        }, 'formation', 'niveau']);

        return view('client.qcms.passer', compact('qcm', 'tentativesFaites'));
    }

    // ===== SOUMETTRE LE QCM =====
    public function soumettre(Request $request, Qcm $qcm)
    {
        $user = auth()->user();

        $inscrit = InscriptionFormation::where('user_id', $user->id)
            ->where('formation_id', $qcm->formation_id)
            ->where('statut', 'valide')
            ->exists();

        abort_if(!$inscrit, 403);

        // Re-vérification de sécurité (au cas où l'utilisateur soumettrait
        // directement une requête sans être passé par demarrer()).
        abort_if(!$this->qcmEstAccessible($qcm, $user->id), 403, $this->messageVerrouillage($qcm));

        $request->validate([
            'reponses'   => 'required|array',
            'reponses.*' => 'array',
        ]);

        $qcm->load('questions.reponses');
        $score    = 0;
        $scoreMax = 0;
        $detail   = [];

        foreach ($qcm->questions as $question) {
            $scoreMax += $question->points;
            $reponsesCorrectes = $question->reponsesCorrectes->pluck('id')->sort()->values();
            $reponsesDonnees   = collect($request->reponses[$question->id] ?? [])->map(fn($id) => (int) $id)->sort()->values();

            $estCorrect = $reponsesCorrectes->count() === $reponsesDonnees->count()
                && $reponsesCorrectes->diff($reponsesDonnees)->isEmpty();

            if ($estCorrect) {
                $score += $question->points;
            }

            $detail[$question->id] = [
                'donnees'   => $reponsesDonnees->toArray(),
                'correctes' => $reponsesCorrectes->toArray(),
                'correct'   => $estCorrect,
                'points'    => $estCorrect ? $question->points : 0,
            ];
        }

        $note   = $scoreMax > 0 ? round(($score / $scoreMax) * 20, 2) : 0;
        $reussi = $note >= $qcm->note_minimale;

        $tentative = SessionQcm::where('qcm_id', $qcm->id)
            ->where('user_id', $user->id)
            ->count() + 1;

        $session = SessionQcm::create([
            'qcm_id'           => $qcm->id,
            'user_id'          => $user->id,
            'reponses_donnees' => $detail,
            'score'            => $score,
            'score_max'        => $scoreMax,
            'note'             => $note,
            'reussi'           => $reussi,
            'tentative'        => $tentative,
            'debut_le'         => now()->subSeconds($request->input('duree_passee', 0)),
            'fin_le'           => now(),
        ]);

        $messageSucces = null;

        if ($reussi) {
            if ($qcm->niveau_id !== null) {
                // ===== QCM DE NIVEAU : jamais de certificat =====
                Notification::create([
                    'user_id' => $user->id,
                    'titre'   => '✅ Niveau validé !',
                    'message' => "Vous avez validé le niveau \"{$qcm->niveau->nom}\" de la formation \"{$qcm->formation->titre}\" avec {$note}/20. Vous pouvez désormais accéder au niveau suivant.",
                    'type'    => 'success',
                ]);

                $messageSucces = "🎉 Niveau validé avec {$note}/20 ! Vous pouvez accéder au niveau suivant.";
            } else {
                // ===== QCM FORMATION ENTIÈRE =====
                if ($qcm->formation->est_payante) {
                    // Certificat uniquement pour les formations payantes
                    $certificat = Certificat::create([
                        'user_id'           => $user->id,
                        'formation_id'      => $qcm->formation_id,
                        'session_qcm_id'    => $session->id,
                        'numero_certificat' => Certificat::genererNumero(),
                        'note_obtenue'      => $note,
                        'delivre_le'        => now(),
                    ]);

                    Notification::create([
                        'user_id' => $user->id,
                        'titre'   => '🎓 Certificat obtenu !',
                        'message' => "Félicitations ! Vous avez obtenu votre certificat pour \"{$qcm->formation->titre}\" avec une note de {$note}/20.",
                        'type'    => 'success',
                    ]);

                    $messageSucces = "🎉 Félicitations ! Vous avez réussi avec {$note}/20 et obtenu votre certificat !";
                } else {
                    // Formation gratuite : réussite enregistrée, mais pas de certificat
                    Notification::create([
                        'user_id' => $user->id,
                        'titre'   => '✅ Formation validée !',
                        'message' => "Vous avez réussi le QCM final de \"{$qcm->formation->titre}\" avec {$note}/20. Cette formation étant gratuite, elle ne donne pas droit à un certificat.",
                        'type'    => 'success',
                    ]);

                    $messageSucces = "🎉 Bravo, vous avez réussi avec {$note}/20 ! Cette formation gratuite ne donne cependant pas droit à un certificat.";
                }
            }
        }

        return redirect()->route('client.qcms.resultat', $session)
            ->with($reussi ? 'success' : 'error',
                $reussi
                    ? $messageSucces
                    : "❌ Score insuffisant : {$note}/20. Note minimale : {$qcm->note_minimale}/20."
            );
    }

    // ===== RÉSULTAT D'UNE SESSION AVEC HISTORIQUE =====
    public function resultat(SessionQcm $session)
    {
        abort_if($session->user_id !== auth()->id(), 403);

        $session->load(['qcm.questions.reponses', 'qcm.formation', 'qcm.niveau', 'certificat']);

        $historique = SessionQcm::where('qcm_id', $session->qcm_id)
            ->where('user_id', auth()->id())
            ->orderByDesc('tentative')
            ->get();

        return view('client.qcms.resultat', compact('session', 'historique'));
    }

    /**
     * ============================================================
     * MÉTHODES PRIVÉES — délèguent aux modèles (source unique de vérité)
     * ============================================================
     * Toute la logique de progression vit dans NiveauFormation::
     * estValidePar() / estAccessiblePar() et Formation::toutesNiveauxValidesPar().
     * Client\ClientController (verrouillage des ressources) utilise exactement
     * les mêmes méthodes, donc les deux endroits ne peuvent jamais diverger.
     */

    /**
     * Un QCM est-il accessible pour cet utilisateur en l'état actuel de sa progression ?
     *
     * - QCM de niveau : accessible si le niveau précédent est validé (le
     *   tout premier niveau est toujours accessible).
     * - QCM de formation entière (niveau_id = null) : accessible seulement
     *   si TOUS les niveaux de la formation sont validés.
     */
    private function qcmEstAccessible(Qcm $qcm, int $userId): bool
    {
        if ($qcm->niveau_id === null) {
            return $qcm->formation->toutesNiveauxValidesPar($userId);
        }

        $niveauActuel = $qcm->niveau ?? NiveauFormation::find($qcm->niveau_id);

        if (!$niveauActuel) {
            // Donnée incohérente (niveau supprimé) : on laisse passer plutôt
            // que de bloquer injustement l'utilisateur.
            return true;
        }

        return $niveauActuel->estAccessiblePar($userId);
    }

    /**
     * Message explicatif à afficher quand un QCM est verrouillé.
     */
    private function messageVerrouillage(Qcm $qcm): string
    {
        if ($qcm->niveau_id === null) {
            return '🔒 Vous devez d\'abord valider tous les niveaux de la formation avant d\'accéder au QCM final.';
        }

        $niveauActuel = $qcm->niveau;

        $niveauPrecedent = $niveauActuel
            ? NiveauFormation::where('formation_id', $qcm->formation_id)
                ->where('ordre', '<', $niveauActuel->ordre)
                ->orderByDesc('ordre')
                ->first()
            : null;

        return $niveauPrecedent
            ? "🔒 Vous devez d'abord valider le niveau \"{$niveauPrecedent->nom}\" avant d'accéder à ce QCM."
            : '🔒 Ce QCM n\'est pas encore accessible.';
    }
}