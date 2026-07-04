<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\Ressource;
use Illuminate\Http\Request;

class ClientFormationController extends Controller
{
    public function index(Request $request)
    {
        $inscriptions = $request->user()
            ->inscriptions()
            ->with(['formation.module', 'formation.niveaux'])
            ->latest()
            ->paginate(10);

        return response()->json($inscriptions);
    }

    public function disponibles(Request $request)
    {
        $formations = Formation::with(['module:id,nom', 'niveaux'])
            ->whereDoesntHave('inscriptions', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->paginate(12);

        return response()->json($formations);
    }

    public function show($id, Request $request)
    {
        $inscription = $request->user()
            ->inscriptions()
            ->where('formation_id', $id)
            ->with(['formation.module', 'formation.niveaux', 'formation.enseignants:id,nom,prenom,avatar'])
            ->firstOrFail();

        $ressources = Ressource::where('formation_id', $id)
            ->where(function ($q) use ($inscription) {
                $q->where('niveau_id', $inscription->niveau_id)
                  ->orWhereNull('niveau_id');
            })
            ->get();

        $qcms = \App\Models\Qcm::where('formation_id', $id)
            ->where('actif', true)
            ->where(function ($q) use ($inscription) {
                $q->where('niveau_id', $inscription->niveau_id)
                  ->orWhereNull('niveau_id');
            })
            ->get();

        return response()->json([
            'inscription' => $inscription,
            'ressources' => $ressources,
            'qcms' => $qcms,
            'progression' => $inscription->progression ?? 0,
        ]);
    }

    public function inscrire(Formation $formation, Request $request)
    {
        $existingInscription = $request->user()
            ->inscriptions()
            ->where('formation_id', $formation->id)
            ->first();

        if ($existingInscription) {
            return response()->json(['message' => 'Déjà inscrit à cette formation.'], 422);
        }

        $inscription = InscriptionFormation::create([
            'user_id' => $request->user()->id,
            'formation_id' => $formation->id,
            'statut' => 'en_attente',
        ]);

        return response()->json([
            'message' => 'Inscription envoyée avec succès.',
            'inscription' => $inscription
        ], 201);
    }

    public function ressources($formationId, Request $request)
    {
        $ressources = Ressource::where('formation_id', $formationId)
            ->with('niveau:id,nom')
            ->orderBy('ordre')
            ->get();

        return response()->json($ressources);
    }

    public function viewPdf(Ressource $ressource, Request $request)
    {
        // Vérifier l'accès
        $hasAccess = $request->user()
            ->inscriptions()
            ->where('formation_id', $ressource->formation_id)
            ->exists();

        if (!$hasAccess) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        return response()->json([
            'url' => asset('storage/' . $ressource->fichier),
            'type' => $ressource->type_fichier,
            'nom' => $ressource->nom,
        ]);
    }
}