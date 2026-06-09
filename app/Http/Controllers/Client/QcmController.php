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
    // ===== LISTE QCMs DISPONIBLES =====
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
                $sessions = SessionQcm::where('qcm_id', $qcm->id)
                    ->where('user_id', $user->id)
                    ->orderByDesc('tentative')
                    ->get();

                $qcm->mes_sessions      = $sessions;
                $qcm->tentatives_faites = $sessions->count();
                $qcm->meilleure_note    = $sessions->max('note');
                $qcm->deja_reussi       = $sessions->where('reussi', true)->count() > 0;
                $qcm->peut_repasser     = $sessions->count() < $qcm->tentatives_max
                                          && !$qcm->deja_reussi;
                return $qcm;
            });

        $certificats = Certificat::where('user_id', $user->id)
            ->with('formation')
            ->latest()
            ->get();

        return view('client.qcms.index', compact('qcms', 'certificats'));
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

    // ===== RÉSULTAT D'UNE SESSION =====
    public function resultat(SessionQcm $session)
    {
        abort_if($session->user_id !== auth()->id(), 403);

        $session->load(['qcm.questions.reponses', 'qcm.formation', 'certificat']);

        return view('client.qcms.resultat', compact('session'));
    }
}