<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use Illuminate\Http\Request;

class AdminPaiementController extends Controller
{
    public function index(Request $request)
    {
        $paiements = Paiement::with(['user:id,nom,prenom,email'])
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->when($request->methode, fn($q) => $q->where('methode', $request->methode))
            ->when($request->date_debut, fn($q) => $q->whereDate('created_at', '>=', $request->date_debut))
            ->when($request->date_fin, fn($q) => $q->whereDate('created_at', '<=', $request->date_fin))
            ->latest()
            ->paginate(20);

        $stats = [
            'total_montant' => Paiement::where('statut', 'complete')->sum('montant'),
            'ce_mois' => Paiement::whereMonth('created_at', now()->month)
                ->where('statut', 'complete')
                ->sum('montant'),
            'en_attente' => Paiement::where('statut', 'en_attente')->count(),
            'complete' => Paiement::where('statut', 'complete')->count(),
            'echoue' => Paiement::where('statut', 'echoue')->count(),
        ];

        return response()->json([
            'paiements' => $paiements,
            'statistiques' => $stats
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:0',
            'methode' => 'required|in:carte,virement,mobile_money,especes',
            'description' => 'nullable|string',
            'type' => 'required|in:formation,service,autre',
            'type_id' => 'nullable|integer',
        ]);

        $paiement = Paiement::create([
            'user_id' => $validated['user_id'],
            'montant' => $validated['montant'],
            'methode' => $validated['methode'],
            'description' => $validated['description'] ?? null,
            'payable_type' => $validated['type'] === 'formation' 
                ? 'App\\Models\\Formation' 
                : ($validated['type'] === 'service' ? 'App\\Models\\Service' : null),
            'payable_id' => $validated['type_id'] ?? null,
            'statut' => 'complete',
            'date_paiement' => now(),
        ]);

        return response()->json([
            'message' => 'Paiement enregistré.',
            'paiement' => $paiement->load('user:id,nom,prenom')
        ], 201);
    }

    public function show(Paiement $paiement)
    {
        return response()->json($paiement->load('user:id,nom,prenom,email'));
    }

    public function update(Paiement $paiement, Request $request)
    {
        $validated = $request->validate([
            'statut' => 'required|in:en_attente,complete,echoue,rembourse',
            'commentaire' => 'nullable|string',
        ]);

        $paiement->update($validated);

        // Si paiement validé, notifier l'utilisateur
        if ($validated['statut'] === 'complete') {
            // $paiement->user->notify(new PaiementValide($paiement));
        }

        return response()->json([
            'message' => 'Paiement mis à jour.',
            'paiement' => $paiement->fresh()
        ]);
    }
}