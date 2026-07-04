<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Qcm;
use App\Models\SessionQcm;
use Illuminate\Http\Request;

class ClientQcmController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $formationsInscrites = $user->inscriptions()
            ->where('statut', 'actif')
            ->pluck('formation_id');

        $qcms = Qcm::whereIn('formation_id', $formationsInscrites)
            ->where('actif', true)
            ->with('formation:id,titre')
            ->get()
            ->map(function ($qcm) use ($user) {
                $session = SessionQcm::where('user_id', $user->id)
                    ->where('qcm_id', $qcm->id)
                    ->latest()
                    ->first();

                return [
                    'id' => $qcm->id,
                    'titre' => $qcm->titre,
                    'formation' => $qcm->formation->titre,
                    'duree' => $qcm->duree,
                    'nombre_questions' => $qcm->questions()->count(),
                    'tentatives' => SessionQcm::where('user_id', $user->id)
                        ->where('qcm_id', $qcm->id)
                        ->count(),
                    'meilleur_score' => SessionQcm::where('user_id', $user->id)
                        ->where('qcm_id', $qcm->id)
                        ->max('score'),
                ];
            });

        return response()->json($qcms);
    }

    public function demarrer(Qcm $qcm, Request $request)
    {
        $session = SessionQcm::create([
            'user_id' => $request->user()->id,
            'qcm_id' => $qcm->id,
            'debut' => now(),
        ]);

        $questions = $qcm->questions()
            ->select('id', 'enonce', 'type', 'options', 'points')
            ->inRandomOrder()
            ->get()
            ->map(function ($q) {
                // Ne pas envoyer la réponse correcte
                unset($q->reponse_correcte);
                return $q;
            });

        return response()->json([
            'session_id' => $session->id,
            'qcm' => [
                'id' => $qcm->id,
                'titre' => $qcm->titre,
                'duree' => $qcm->duree,
                'questions' => $questions,
            ]
        ]);
    }

    public function soumettre(Qcm $qcm, Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:session_qcms,id',
            'reponses' => 'required|array',
            'reponses.*.question_id' => 'required|exists:questions,id',
            'reponses.*.reponse' => 'required',
        ]);

        $session = SessionQcm::findOrFail($request->session_id);
        
        $score = 0;
        $totalPoints = 0;
        $details = [];

        foreach ($request->reponses as $reponse) {
            $question = \App\Models\Question::find($reponse['question_id']);
            $correct = $question->reponse_correcte === $reponse['reponse'];
            
            if ($correct) {
                $score += $question->points;
            }
            $totalPoints += $question->points;

            $details[] = [
                'question_id' => $question->id,
                'enonce' => $question->enonce,
                'votre_reponse' => $reponse['reponse'],
                'bonne_reponse' => $question->reponse_correcte,
                'correct' => $correct,
                'points' => $correct ? $question->points : 0,
            ];
        }

        $pourcentage = ($totalPoints > 0) ? round(($score / $totalPoints) * 100, 2) : 0;

        $session->update([
            'fin' => now(),
            'score' => $pourcentage,
            'reussite' => $pourcentage >= ($qcm->seuil_reussite ?? 50),
        ]);

        // Générer certificat si réussi
        if ($session->reussite) {
            $this->genererCertificat($request->user(), $qcm, $pourcentage);
        }

        return response()->json([
            'score' => $pourcentage,
            'reussite' => $session->reussite,
            'total_points' => $totalPoints,
            'points_obtenus' => $score,
            'details' => $details,
        ]);
    }

    public function resultat(SessionQcm $session, Request $request)
    {
        if ($session->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        return response()->json([
            'qcm' => $session->qcm->titre,
            'score' => $session->score,
            'reussite' => $session->reussite,
            'duree' => $session->debut->diffInMinutes($session->fin),
            'date' => $session->created_at,
        ]);
    }

    private function genererCertificat($user, $qcm, $score)
    {
        $exists = \App\Models\Certificat::where('user_id', $user->id)
            ->where('qcm_id', $qcm->id)
            ->exists();

        if (!$exists) {
            \App\Models\Certificat::create([
                'user_id' => $user->id,
                'formation_id' => $qcm->formation_id,
                'qcm_id' => $qcm->id,
                'score' => $score,
                'date_obtention' => now(),
            ]);
        }
    }
}