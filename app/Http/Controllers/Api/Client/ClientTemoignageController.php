<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Temoignage;
use Illuminate\Http\Request;

class ClientTemoignageController extends Controller
{
    public function index(Request $request)
    {
        $temoignages = $request->user()
            ->temoignages()
            ->latest()
            ->get();

        return response()->json($temoignages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contenu' => 'required|string|min:10|max:500',
            'note' => 'required|integer|min:1|max:5',
            'formation_id' => 'nullable|exists:formations,id',
        ]);

        $temoignage = $request->user()->temoignages()->create([
            'contenu' => $validated['contenu'],
            'note' => $validated['note'],
            'formation_id' => $validated['formation_id'] ?? null,
            'valide' => false, // En attente de modération
        ]);

        return response()->json([
            'message' => 'Témoignage envoyé. En attente de validation.',
            'temoignage' => $temoignage
        ], 201);
    }
}