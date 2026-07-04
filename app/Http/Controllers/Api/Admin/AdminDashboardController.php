<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Formation;
use App\Models\Service;
use App\Models\Paiement;
use App\Models\DemandeService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'statistiques' => [
                'total_clients' => User::role('client')->count(),
                'total_enseignants' => User::role('enseignant')->count(),
                'total_formations' => Formation::count(),
                'total_services' => Service::count(),
                'inscriptions_en_attente' => \App\Models\InscriptionFormation::where('statut', 'en_attente')->count(),
                'demandes_en_attente' => DemandeService::where('statut', 'en_attente')->count(),
                'paiements_mois' => Paiement::whereMonth('created_at', now()->month)
                    ->where('statut', 'complete')
                    ->sum('montant'),
                'certificats_delivres' => \App\Models\Certificat::count(),
            ],
            'derniers_utilisateurs' => User::latest()->take(5)->get(['id', 'nom', 'prenom', 'email', 'created_at']),
            'derniers_paiements' => Paiement::with('user:id,nom,prenom')
                ->latest()
                ->take(5)
                ->get(),
            'formations_populaires' => Formation::withCount('inscriptions')
                ->orderBy('inscriptions_count', 'desc')
                ->take(5)
                ->get(['id', 'titre', 'image']),
        ]);
    }
}