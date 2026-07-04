<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandeService;
use Illuminate\Http\Request;

class AdminDemandeController extends Controller
{
    public function index(Request $request)
    {
        $demandes = DemandeService::with(['service:id,nom', 'user:id,nom,prenom,email'])
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->when($request->service_id, fn($q) => $q->where('service_id', $request->service_id))
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => DemandeService::count(),
            'en_attente' => DemandeService::where('statut', 'en_attente')->count(),
            'en_cours' => DemandeService::where('statut', 'en_cours')->count(),
            'terminees' => DemandeService::where('statut', 'termine')->count(),
            'annulees' => DemandeService::where('statut', 'annule')->count(),
        ];

        return response()->json([
            'demandes' => $demandes,
            'statistiques' => $stats
        ]);
    }

    public function show(DemandeService $demande)
    {
        $demande->load(['service', 'user:id,nom,prenom,email,telephone']);
        
        return response()->json($demande);
    }

    public function changerStatut(DemandeService $demande, Request $request)
    {
        $validated = $request->validate([
            'statut' => 'required|in:en_attente,en_cours,termine,annule',
            'commentaire' => 'nullable|string',
        ]);

        $demande->update([
            'statut' => $validated['statut'],
            'commentaire_admin' => $validated['commentaire'] ?? null,
        ]);

        // Notifier le client
        // $demande->user->notify(new DemandeStatutChange($demande));

        return response()->json([
            'message' => 'Statut mis à jour.',
            'demande' => $demande->fresh()
        ]);
    }
}