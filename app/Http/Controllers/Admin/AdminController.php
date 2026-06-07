<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandeService;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\Paiement;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // ===== STATS GLOBALES (requêtes agrégées optimisées) =====
        $statsUsers = User::selectRaw("
            SUM(CASE WHEN roles.name = 'client' THEN 1 ELSE 0 END) as clients,
            SUM(CASE WHEN roles.name = 'enseignant' THEN 1 ELSE 0 END) as enseignants
        ")
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->first();

        $statsDemandes = DemandeService::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
            SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
            SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) as terminees
        ")->first();

        $stats = [
            'clients'      => $statsUsers->clients ?? 0,
            'enseignants'  => $statsUsers->enseignants ?? 0,
            'services'     => Service::where('actif', true)->count(),
            'formations'   => Formation::where('statut', 'publie')->count(),
            'demandes'     => $statsDemandes->total ?? 0,
            'en_attente'   => $statsDemandes->en_attente ?? 0,
            'en_cours'     => $statsDemandes->en_cours ?? 0,
            'terminees'    => $statsDemandes->terminees ?? 0,
            'inscriptions' => InscriptionFormation::count(),
            'revenus'      => Paiement::sum('montant_paye'),
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
        $moisFr = [
            '', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun',
            'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'
        ];

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

        $labelsServices   = [];
        $dataServices     = [];
        $categoriesLabels = [
            'bureautique' => 'Bureautique',
            'design'      => 'Design',
            'web_mobile'  => 'Web & Mobile',
        ];

        foreach ($repartitionServices as $item) {
            $labelsServices[] = $categoriesLabels[$item->categorie] ?? $item->categorie;
            $dataServices[]   = $item->total;
        }

        // ===== INSCRIPTIONS RÉCENTES (eager loading optimisé) =====
        $inscriptionsRecentes = InscriptionFormation::with([
            'user:id,nom,prenom',
            'formation:id,titre'
        ])->latest()->take(5)->get();

        // ===== DEMANDES EN ATTENTE (eager loading optimisé) =====
        $demandesEnAttente = DemandeService::with([
            'service:id,titre,icone',
            'user:id,nom,prenom'
        ])
        ->where('statut', 'en_attente')
        ->latest()->take(5)->get();

        // ===== ALERTES =====
        $alertes = [];

        if (($stats['en_attente'] ?? 0) > 10) {
            $alertes[] = [
                'type'    => 'warning',
                'message' => "{$stats['en_attente']} demandes en attente de traitement.",
            ];
        }

        $clientsSansVerif = User::role('client')
            ->whereNull('email_verified_at')
            ->count();

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