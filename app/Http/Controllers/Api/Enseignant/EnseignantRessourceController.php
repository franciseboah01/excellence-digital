<?php

namespace App\Http\Controllers\Api\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Ressource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EnseignantRessourceController extends Controller
{
    public function index(Request $request)
    {
        $ressources = $request->user()
            ->ressources()
            ->with(['formation:id,titre', 'niveau:id,nom'])
            ->when($request->formation_id, fn($q) => $q->where('formation_id', $request->formation_id))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->orderBy('ordre')
            ->paginate(20);

        return response()->json($ressources);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'formation_id' => 'required|exists:formations,id',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'type' => 'required|in:pdf,video,lien,document,image',
            'fichier' => 'required_if:type,pdf,document,image|file|max:50000',
            'lien' => 'required_if:type,video,lien|url',
            'ordre' => 'nullable|integer',
            'visible' => 'boolean',
        ]);

        // Vérifier que l'enseignant est assigné à cette formation
        $isAssigned = $request->user()
            ->formationsEnseignant()
            ->where('formation_id', $validated['formation_id'])
            ->exists();

        if (!$isAssigned) {
            return response()->json([
                'message' => 'Vous n\'êtes pas assigné à cette formation.'
            ], 403);
        }

        $data = [
            'enseignant_id' => $request->user()->id,
            'formation_id' => $validated['formation_id'],
            'niveau_id' => $validated['niveau_id'] ?? null,
            'nom' => $validated['nom'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'ordre' => $validated['ordre'] ?? 0,
            'visible' => $validated['visible'] ?? true,
        ];

        // Gestion du fichier
        if ($request->hasFile('fichier')) {
            $data['fichier'] = $request->file('fichier')
                ->store('ressources/' . $validated['formation_id'], 'public');
            $data['type_fichier'] = $request->file('fichier')->getClientOriginalExtension();
        }

        // Gestion du lien
        if (in_array($validated['type'], ['video', 'lien'])) {
            $data['lien'] = $validated['lien'];
        }

        $ressource = Ressource::create($data);

        return response()->json([
            'message' => 'Ressource créée avec succès.',
            'ressource' => $ressource->load(['formation:id,titre', 'niveau:id,nom'])
        ], 201);
    }

    public function show(Ressource $ressource, Request $request)
    {
        if ($ressource->enseignant_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        return response()->json($ressource->load(['formation:id,titre', 'niveau:id,nom']));
    }

    public function update(Ressource $ressource, Request $request)
    {
        if ($ressource->enseignant_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'type' => 'sometimes|in:pdf,video,lien,document,image',
            'fichier' => 'nullable|file|max:50000',
            'lien' => 'nullable|url',
            'ordre' => 'nullable|integer',
            'visible' => 'boolean',
        ]);

        $data = array_intersect_key($validated, array_flip([
            'nom', 'description', 'niveau_id', 'type', 'ordre', 'visible'
        ]));

        // Gestion du fichier
        if ($request->hasFile('fichier')) {
            // Supprimer l'ancien fichier
            if ($ressource->fichier) {
                Storage::disk('public')->delete($ressource->fichier);
            }
            $data['fichier'] = $request->file('fichier')
                ->store('ressources/' . $ressource->formation_id, 'public');
            $data['type_fichier'] = $request->file('fichier')->getClientOriginalExtension();
        }

        // Gestion du lien
        if (isset($validated['lien'])) {
            $data['lien'] = $validated['lien'];
        }

        $ressource->update($data);

        return response()->json([
            'message' => 'Ressource mise à jour.',
            'ressource' => $ressource->fresh()->load(['formation:id,titre', 'niveau:id,nom'])
        ]);
    }

    public function destroy(Ressource $ressource, Request $request)
    {
        if ($ressource->enseignant_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        // Supprimer le fichier
        if ($ressource->fichier) {
            Storage::disk('public')->delete($ressource->fichier);
        }

        $ressource->delete();

        return response()->json(['message' => 'Ressource supprimée.']);
    }
}