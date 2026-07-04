<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Niveau;
use App\Models\InscriptionFormation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminFormationController extends Controller
{
    public function index(Request $request)
    {
        $formations = Formation::with(['module:id,nom', 'niveaux', 'enseignants:id,nom,prenom'])
            ->withCount('inscriptions')
            ->when($request->module_id, fn($q) => $q->where('module_id', $request->module_id))
            ->when($request->search, fn($q) => $q->where('titre', 'LIKE', "%{$request->search}%"))
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->latest()
            ->paginate(15);

        return response()->json($formations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'module_id' => 'required|exists:modules,id',
            'image' => 'nullable|image|max:2048',
            'prix' => 'nullable|numeric|min:0',
            'duree' => 'nullable|string|max:100',
            'prerequis' => 'nullable|string',
            'objectifs' => 'nullable|array',
            'statut' => 'in:brouillon,publie,archive',
        ]);

        $validated['slug'] = Str::slug($validated['titre']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('formations', 'public');
        }

        $formation = Formation::create($validated);

        return response()->json([
            'message' => 'Formation créée avec succès.',
            'formation' => $formation->load(['module', 'niveaux'])
        ], 201);
    }

    public function show(Formation $formation)
    {
        $formation->load([
            'module',
            'niveaux',
            'enseignants:id,nom,prenom,email,avatar',
            'inscriptions' => function ($q) {
                $q->with('user:id,nom,prenom,email,avatar')->latest();
            },
            'ressources' => function ($q) {
                $q->with('enseignant:id,nom,prenom')->orderBy('ordre');
            },
            'qcms' => function ($q) {
                $q->withCount('questions');
            }
        ]);

        $stats = [
            'total_inscriptions' => $formation->inscriptions()->count(),
            'inscriptions_actives' => $formation->inscriptions()->where('statut', 'actif')->count(),
            'inscriptions_terminees' => $formation->inscriptions()->where('statut', 'termine')->count(),
            'inscriptions_en_attente' => $formation->inscriptions()->where('statut', 'en_attente')->count(),
            'taux_reussite_qcms' => $formation->qcms()
                ->whereHas('sessions', fn($q) => $q->where('reussite', true))
                ->count(),
            'total_ressources' => $formation->ressources()->count(),
        ];

        return response()->json([
            'formation' => $formation,
            'statistiques' => $stats
        ]);
    }

    public function update(Formation $formation, Request $request)
    {
        $validated = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'module_id' => 'sometimes|exists:modules,id',
            'image' => 'nullable|image|max:2048',
            'prix' => 'nullable|numeric|min:0',
            'duree' => 'nullable|string|max:100',
            'prerequis' => 'nullable|string',
            'objectifs' => 'nullable|array',
            'statut' => 'in:brouillon,publie,archive',
        ]);

        if (isset($validated['titre'])) {
            $validated['slug'] = Str::slug($validated['titre']);
        }

        if ($request->hasFile('image')) {
            if ($formation->image) {
                \Storage::disk('public')->delete($formation->image);
            }
            $validated['image'] = $request->file('image')->store('formations', 'public');
        }

        $formation->update($validated);

        return response()->json([
            'message' => 'Formation mise à jour.',
            'formation' => $formation->fresh()->load(['module', 'niveaux'])
        ]);
    }

    public function destroy(Formation $formation)
    {
        if ($formation->inscriptions()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer une formation avec des inscriptions.'
            ], 422);
        }

        if ($formation->image) {
            \Storage::disk('public')->delete($formation->image);
        }

        $formation->niveaux()->delete();
        $formation->delete();

        return response()->json(['message' => 'Formation supprimée.']);
    }

    public function storeNiveau(Formation $formation, Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordre' => 'nullable|integer',
        ]);

        $niveau = $formation->niveaux()->create([
            'nom' => $validated['nom'],
            'description' => $validated['description'] ?? null,
            'ordre' => $validated['ordre'] ?? ($formation->niveaux()->max('ordre') + 1),
        ]);

        return response()->json([
            'message' => 'Niveau ajouté.',
            'niveau' => $niveau
        ], 201);
    }

    public function destroyNiveau(Niveau $niveau)
    {
        if ($niveau->inscriptions()->count() > 0 || $niveau->ressources()->count() > 0) {
            return response()->json([
                'message' => 'Ce niveau est lié à des inscriptions ou ressources.'
            ], 422);
        }

        $niveau->delete();

        return response()->json(['message' => 'Niveau supprimé.']);
    }

    public function assignerEnseignant(Formation $formation, Request $request)
    {
        $request->validate([
            'enseignant_id' => 'required|exists:users,id',
        ]);

        $enseignant = User::findOrFail($request->enseignant_id);

        if (!$enseignant->hasRole('enseignant')) {
            return response()->json([
                'message' => 'L\'utilisateur n\'est pas un enseignant.'
            ], 422);
        }

        // Vérifier si déjà assigné
        if ($formation->enseignants()->where('user_id', $enseignant->id)->exists()) {
            return response()->json([
                'message' => 'Cet enseignant est déjà assigné à cette formation.'
            ], 422);
        }

        $formation->enseignants()->attach($enseignant->id);

        return response()->json([
            'message' => 'Enseignant assigné avec succès.',
            'enseignant' => $enseignant->only(['id', 'nom', 'prenom', 'email'])
        ]);
    }

    public function retirerEnseignant(Formation $formation, User $enseignant)
    {
        if (!$formation->enseignants()->where('user_id', $enseignant->id)->exists()) {
            return response()->json([
                'message' => 'Cet enseignant n\'est pas assigné à cette formation.'
            ], 422);
        }

        $formation->enseignants()->detach($enseignant->id);

        return response()->json(['message' => 'Enseignant retiré de la formation.']);
    }

    public function validerInscription(InscriptionFormation $inscription)
    {
        $inscription->update(['statut' => 'actif', 'date_validation' => now()]);

        // Notifier l'utilisateur
        // $inscription->user->notify(new InscriptionValidee($inscription));

        return response()->json([
            'message' => 'Inscription validée.',
            'inscription' => $inscription
        ]);
    }

    public function rejeterInscription(InscriptionFormation $inscription, Request $request)
    {
        $request->validate([
            'motif' => 'nullable|string',
        ]);

        $inscription->update([
            'statut' => 'rejete',
            'motif_rejet' => $request->motif ?? null
        ]);

        return response()->json([
            'message' => 'Inscription rejetée.',
            'inscription' => $inscription
        ]);
    }
}