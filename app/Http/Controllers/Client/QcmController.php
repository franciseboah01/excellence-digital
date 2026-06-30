<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Certificat;
use App\Models\InscriptionFormation;
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

        // Formations validées du client
        $formationIds = InscriptionFormation::where('user_id', $user->id)
            ->where('statut', 'valide')
            ->pluck('formation_id');

        $qcms = Qcm::whereIn('formation_id', $formationIds)
            ->where('actif', true)
            ->with(['formation', 'niveau'])
            ->withCount('questions')
            ->get()
            ->map(function ($qcm) use ($user) {
                // ===== RÉCUPÉRER TOUTES LES SESSIONS =====
                $sessions = SessionQcm::where('qcm_id', $qcm->id)
                    ->where('user_id', $user->id)
                    ->orderByDesc('tentative')
                    ->get();

                // ===== STATISTIQUES =====
                $qcm->mes_sessions      = $sessions;
                $qcm->tentatives_faites = $sessions->count();
                $qcm->meilleure_note    = $sessions->max('note');
                $qcm->derniere_note     = $sessions->first()?->note;
                $qcm->derniere_tentative = $sessions->first()?->created_at;
                
                // ===== STATUT =====
                $qcm->deja_reussi       = $sessions->where('reussi', true)->count() > 0;
                $qcm->peut_repasser     = $sessions->count() < $qcm->tentatives_max
                                          && !$qcm->deja_reussi;
                
                // ===== SI RÉUSSI, RÉCUPÉRER LE CERTIFICAT =====
                if ($qcm->deja_reussi) {
                    $sessionReussie = $sessions->where('reussi', true)->first();
                    $qcm->certificat = Certificat::where('session_qcm_id', $sessionReussie?->id)
                        ->where('user_id', $user->id)
                        ->first();
                } else {
                    $qcm->certificat = null;
                }

                return $qcm;
            });

        // ===== STATISTIQUES GLOBALES =====
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

        // Vérifier inscription
        $inscrit = InscriptionFormation::where('user_id', $user->id)
            ->where('formation_id', $qcm->formation_id)
            ->where('statut', 'valide')
            ->exists();

        abort_if(!$inscrit, 403, 'Vous n\'êtes pas inscrit à cette formation.');
        abort_if(!$qcm->actif, 403, 'Ce QCM n\'est pas disponible.');

        // Vérifier tentatives
        $tentativesFaites = SessionQcm::where('qcm_id', $qcm->id)
            ->where('user_id', $user->id)
            ->count();

        $dejaReussi = SessionQcm::where('qcm_id', $qcm->id)
            ->where('user_id', $user->id)
            ->where('reussi', true)
            ->exists();

        if ($dejaReussi) {
            return redirect()->route('client.qcms.index')
                ->with('info', '🎉 Vous avez déjà réussi ce QCM et obtenu votre certificat !');
        }

        if ($tentativesFaites >= $qcm->tentatives_max) {
            return redirect()->route('client.qcms.index')
                ->with('error', "❌ Vous avez épuisé vos {$qcm->tentatives_max} tentatives.");
        }

        // Charger questions avec réponses mélangées
        $qcm->load(['questions' => function ($q) {
            $q->orderBy('ordre')->with(['reponses' => function ($r) {
                $r->inRandomOrder(); // Mélanger les réponses
            }]);
        }, 'formation', 'niveau']);

        return view('client.qcms.passer', compact('qcm', 'tentativesFaites'));
    }

    // ===== SOUMETTRE LE QCM =====
    public function soumettre(Request $request, Qcm $qcm)
    {
        $user = auth()->user();

        // Vérifications de sécurité
        $inscrit = InscriptionFormation::where('user_id', $user->id)
            ->where('formation_id', $qcm->formation_id)
            ->where('statut', 'valide')
            ->exists();

        abort_if(!$inscrit, 403);

        $request->validate([
            'reponses'   => 'required|array',
            'reponses.*' => 'array',
        ]);

        // Calculer le score
        $qcm->load('questions.reponses');
        $score    = 0;
        $scoreMax = 0;
        $detail   = [];

        foreach ($qcm->questions as $question) {
            $scoreMax += $question->points;
            $reponsesCorrectes = $question->reponsesCorrectes->pluck('id')->sort()->values();
            $reponsesDonnees   = collect($request->reponses[$question->id] ?? [])->map(fn($id) => (int)$id)->sort()->values();

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

        // Calculer la note sur 20
        $note   = $scoreMax > 0 ? round(($score / $scoreMax) * 20, 2) : 0;
        $reussi = $note >= $qcm->note_minimale;

        $tentative = SessionQcm::where('qcm_id', $qcm->id)
            ->where('user_id', $user->id)
            ->count() + 1;

        // Enregistrer la session
        $session = SessionQcm::create([
            'qcm_id'          => $qcm->id,
            'user_id'         => $user->id,
            'reponses_donnees'=> $detail,
            'score'           => $score,
            'score_max'       => $scoreMax,
            'note'            => $note,
            'reussi'          => $reussi,
            'tentative'       => $tentative,
            'debut_le'        => now()->subSeconds($request->input('duree_passee', 0)),
            'fin_le'          => now(),
        ]);

        // Générer le certificat si réussi
        if ($reussi) {
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
        }

        return redirect()->route('client.qcms.resultat', $session)
            ->with($reussi ? 'success' : 'error',
                $reussi
                    ? "🎉 Félicitations ! Vous avez réussi avec {$note}/20 !"
                    : "❌ Score insuffisant : {$note}/20. Note minimale : {$qcm->note_minimale}/20."
            );
    }

    // ===== RÉSULTAT D'UNE SESSION AVEC HISTORIQUE =====
    public function resultat(SessionQcm $session)
    {
        abort_if($session->user_id !== auth()->id(), 403);

        $session->load(['qcm.questions.reponses', 'qcm.formation', 'certificat']);

        // Récupérer l'historique des tentatives pour ce QCM
        $historique = SessionQcm::where('qcm_id', $session->qcm_id)
            ->where('user_id', auth()->id())
            ->orderByDesc('tentative')
            ->get();

        return view('client.qcms.resultat', compact('session', 'historique'));
    }
}