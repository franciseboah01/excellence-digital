<?php

namespace App\Http\Controllers\Api\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Qcm;
use App\Models\Question;
use Illuminate\Http\Request;

class EnseignantQcmController extends Controller
{
    public function index(Request $request)
    {
        $qcms = Qcm::where('createur_id', $request->user()->id)
            ->with(['formation:id,titre', 'niveau:id,nom'])
            ->withCount('questions')
            ->when($request->formation_id, fn($q) => $q->where('formation_id', $request->formation_id))
            ->latest()
            ->paginate(15);

        return response()->json($qcms);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'formation_id' => 'required|exists:formations,id',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'duree' => 'required|integer|min:1', // en minutes
            'seuil_reussite' => 'required|integer|min:0|max:100',
            'actif' => 'boolean',
        ]);

        $qcm = Qcm::create([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'formation_id' => $validated['formation_id'],
            'niveau_id' => $validated['niveau_id'] ?? null,
            'createur_id' => $request->user()->id,
            'duree' => $validated['duree'],
            'seuil_reussite' => $validated['seuil_reussite'],
            'actif' => $validated['actif'] ?? false,
        ]);

        return response()->json([
            'message' => 'QCM créé avec succès.',
            'qcm' => $qcm->load(['formation:id,titre', 'niveau:id,nom'])
        ], 201);
    }

    public function show(Qcm $qcm, Request $request)
    {
        if ($qcm->createur_id !== $request->user()->id && !$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $qcm->load(['formation:id,titre', 'niveau:id,nom', 'questions' => function ($q) {
            $q->orderBy('ordre');
        }, 'sessions' => function ($q) {
            $q->with('user:id,nom,prenom,email')->latest()->take(20);
        }]);

        return response()->json($qcm);
    }

    public function update(Qcm $qcm, Request $request)
    {
        if ($qcm->createur_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $validated = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'duree' => 'sometimes|integer|min:1',
            'seuil_reussite' => 'sometimes|integer|min:0|max:100',
            'actif' => 'boolean',
        ]);

        $qcm->update($validated);

        return response()->json([
            'message' => 'QCM mis à jour.',
            'qcm' => $qcm->fresh()->load(['formation:id,titre', 'niveau:id,nom'])
        ]);
    }

    public function destroy(Qcm $qcm, Request $request)
    {
        if ($qcm->createur_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $qcm->questions()->delete();
        $qcm->delete();

        return response()->json(['message' => 'QCM supprimé.']);
    }

    public function storeQuestion(Qcm $qcm, Request $request)
    {
        if ($qcm->createur_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $validated = $request->validate([
            'enonce' => 'required|string',
            'type' => 'required|in:qcm_unique,qcm_multiple,vrai_faux,texte',
            'options' => 'required_if:type,qcm_unique,qcm_multiple|array',
            'options.*' => 'required|string',
            'reponse_correcte' => 'required|string',
            'points' => 'required|integer|min:1',
            'ordre' => 'nullable|integer',
            'explication' => 'nullable|string',
        ]);

        $question = $qcm->questions()->create([
            'enonce' => $validated['enonce'],
            'type' => $validated['type'],
            'options' => $validated['options'] ?? null,
            'reponse_correcte' => $validated['reponse_correcte'],
            'points' => $validated['points'],
            'ordre' => $validated['ordre'] ?? ($qcm->questions()->max('ordre') + 1),
            'explication' => $validated['explication'] ?? null,
        ]);

        return response()->json([
            'message' => 'Question ajoutée.',
            'question' => $question
        ], 201);
    }

    public function updateQuestion(Question $question, Request $request)
    {
        $qcm = $question->qcm;
        if ($qcm->createur_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $validated = $request->validate([
            'enonce' => 'sometimes|string',
            'type' => 'sometimes|in:qcm_unique,qcm_multiple,vrai_faux,texte',
            'options' => 'nullable|array',
            'reponse_correcte' => 'sometimes|string',
            'points' => 'sometimes|integer|min:1',
            'ordre' => 'nullable|integer',
            'explication' => 'nullable|string',
        ]);

        $question->update($validated);

        return response()->json([
            'message' => 'Question mise à jour.',
            'question' => $question->fresh()
        ]);
    }

    public function destroyQuestion(Question $question, Request $request)
    {
        if ($question->qcm->createur_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $question->delete();

        return response()->json(['message' => 'Question supprimée.']);
    }

    public function toggleActif(Qcm $qcm, Request $request)
    {
        if ($qcm->createur_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        if ($qcm->questions()->count() === 0) {
            return response()->json([
                'message' => 'Impossible d\'activer un QCM sans questions.'
            ], 422);
        }

        $qcm->update(['actif' => !$qcm->actif]);

        return response()->json([
            'message' => $qcm->actif ? 'QCM activé.' : 'QCM désactivé.',
            'actif' => $qcm->actif
        ]);
    }

    public function resultats(Qcm $qcm, Request $request)
    {
        if ($qcm->createur_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $sessions = $qcm->sessions()
            ->with('user:id,nom,prenom,email')
            ->latest()
            ->paginate(20);

        $stats = [
            'total_tentatives' => $qcm->sessions()->count(),
            'taux_reussite' => $qcm->sessions()->where('reussite', true)->count(),
            'score_moyen' => round($qcm->sessions()->avg('score'), 2),
            'score_max' => $qcm->sessions()->max('score'),
            'score_min' => $qcm->sessions()->min('score'),
        ];

        return response()->json([
            'sessions' => $sessions,
            'statistiques' => $stats
        ]);
    }
}