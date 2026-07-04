<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Certificat;
use Illuminate\Http\Request;

class ClientCertificatController extends Controller
{
    public function index(Request $request)
    {
        $certificats = $request->user()
            ->certificats()
            ->with(['formation:id,titre,image'])
            ->latest()
            ->paginate(10);

        return response()->json($certificats);
    }

    public function telecharger(Certificat $certificat, $format = 'pdf', Request $request)
    {
        if ($certificat->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        // Logique de génération PDF
        return response()->json([
            'message' => 'Téléchargement initié.',
            'url' => route('certificats.telecharger', $certificat),
        ]);
    }
}