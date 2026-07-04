<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\DemandeService;
use Illuminate\Http\Request;

class ClientDemandeController extends Controller
{
    public function index(Request $request)
    {
        $demandes = $request->user()
            ->demandesService()
            ->with('service:id,nom')
            ->latest()
            ->paginate(10);

        return response()->json($demandes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'description' => 'required|string|min:10',
            'date_souhaitee' => 'nullable|date',
            'budget' => 'nullable|numeric',
        ]);

        $demande = $request->user()->demandesService()->create($validated);

        return response()->json([
            'message' => 'Demande créée avec succès.',
            'demande' => $demande
        ], 201);
    }
}