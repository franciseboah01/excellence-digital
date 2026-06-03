<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandeService;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // ===== STATS GLOBALES =====
        $stats = [
            'clients'      => User::role('client')->count(),
            'enseignants'  => User::role('enseignant')->count(),
            'services'     => Service::where('actif', true)->count(),
            'formations'   => Formation::where('statut', 'publie')->count(),
            'demandes'     => DemandeService::count(),
            'en_attente'   => DemandeService::where('statut', 'en_attente')->count(),
            'en_cours'     => DemandeService::where('statut', 'en_cours')->count(),
            'terminees'    => DemandeService::where('statut', 'termine')->count(),
            'inscriptions' => InscriptionFormation::count(),
            'revenus'      => DemandeService::where('statut', 'termine')
                                ->join('services', 'demandes_service.service_id', '=', 'services.id')
                                ->sum('services.prix'),
        ];

        // ===== INSCRIPTIONS PAR MOIS (6 derniers mois) =====
        $inscriptionsMois = InscriptionFormation::select(
            DB::raw('MONTH(created_at) as mois'),
            DB::raw('YEAR(created_at) as annee'),
            DB::raw('COUNT(*) as total')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('annee', 'mois')
        ->orderBy('annee')
        ->orderBy('mois')
        ->get();

        $labelsMois = [];
        $dataMois   = [];
        $moisFr = ['', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun',
                       'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

        foreach ($inscriptionsMois as $item) {
            $labelsMois[] = $moisFr[$item->mois] . ' ' . $item->annee;
            $dataMois[]   = $item->total;
        }

        // ===== RÉPARTITION DES SERVICES =====
        $repartitionServices = DemandeService::select(
            'services.categorie',
            DB::raw('COUNT(*) as total')
        )
        ->join('services', 'demandes_service.service_id', '=', 'services.id')
        ->groupBy('services.categorie')
        ->get();

        $labelsServices = [];
        $dataServices   = [];
        $categoriesLabels = [
            'bureautique' => 'Bureautique',
            'design'      => 'Design',
            'web_mobile'  => 'Web & Mobile',
        ];

        foreach ($repartitionServices as $item) {
            $labelsServices[] = $categoriesLabels[$item->categorie] ?? $item->categorie;
            $dataServices[]   = $item->total;
        }

        // ===== INSCRIPTIONS RÉCENTES =====
        $inscriptionsRecentes = InscriptionFormation::with(['user', 'formation'])
            ->latest()->take(5)->get();

        // ===== DEMANDES EN ATTENTE =====
        $demandesEnAttente = DemandeService::with(['service', 'user'])
            ->where('statut', 'en_attente')
            ->latest()->take(5)->get();

        // ===== ALERTES =====
        $alertes = [];

        if ($stats['en_attente'] > 10) {
            $alertes[] = [
                'type'    => 'warning',
                'message' => "{$stats['en_attente']} demandes en attente de traitement.",
            ];
        }

        $clientsSansVerif = User::role('client')
            ->whereNull('email_verified_at')->count();

        if ($clientsSansVerif > 0) {
            $alertes[] = [
                'type'    => 'info',
                'message' => "{$clientsSansVerif} client(s) n'ont pas encore vérifié leur email.",
            ];
        }

        return view('admin.dashboard', compact(
            'stats',
            'labelsMois', 'dataMois',
            'labelsServices', 'dataServices',
            'inscriptionsRecentes',
            'demandesEnAttente',
            'alertes'
        ));
    }
}