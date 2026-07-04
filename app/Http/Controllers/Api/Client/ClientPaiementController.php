<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use Illuminate\Http\Request;

class ClientPaiementController extends Controller
{
    public function index(Request $request)
    {
        $paiements = Paiement::where('user_id', $request->user()->id)
            ->with(['formation:id,titre', 'service:id,nom'])
            ->latest()
            ->paginate(10);

        return response()->json($paiements);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:formation,service',
            'type_id' => 'required|integer',
            'montant' => 'required|numeric|min:0',
            'methode' => 'required|in:carte,virement,mobile_money',
        ]);

        $paiement = Paiement::create([
            'user_id' => $request->user()->id,
            'payable_type' => $validated['type'] === 'formation' 
                ? 'App\\Models\\Formation' 
                : 'App\\Models\\Service',
            'payable_id' => $validated['type_id'],
            'montant' => $validated['montant'],
            'methode' => $validated['methode'],
            'statut' => 'en_attente',
        ]);

        return response()->json([
            'message' => 'Paiement initié. En attente de validation.',
            'paiement' => $paiement
        ], 201);
    }
}