<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StatutDemandeMail;
use App\Models\DemandeService;
use App\Models\Notification;
use App\Models\Paiement;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DemandeController extends Controller
{
    // Transitions autorisées
    private array $transitionsAutorisees = [
        'en_attente' => ['en_cours', 'annule'],
        'en_cours'   => ['termine', 'annule'],
        'termine'    => [],
        'annule'     => [],
    ];

    private function transitionAutorisee(string $actuel, string $nouveau): bool
    {
        return in_array($nouveau, $this->transitionsAutorisees[$actuel] ?? []);
    }

    // ===== LISTE =====
    public function index(Request $request)
    {
        $query = DemandeService::with(['service.categorie', 'user']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('search')) {
            $search = substr(trim($request->search), 0, 50);
            $query->where(function ($q) use ($search) {
                $q->where('nom_visiteur', 'like', "%{$search}%")
                  ->orWhere('email_visiteur', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $demandes = $query->latest()->paginate(15)->withQueryString();
        $services = Service::where('actif', true)->get();

        $stats = [
            'total'      => DemandeService::count(),
            'en_attente' => DemandeService::where('statut', 'en_attente')->count(),
            'en_cours'   => DemandeService::where('statut', 'en_cours')->count(),
            'terminees'  => DemandeService::where('statut', 'termine')->count(),
            'annulees'   => DemandeService::where('statut', 'annule')->count(),
        ];

        // ✅ On expose, pour chaque demande, le montant déjà payé — utile pour
        // que la vue affiche clairement si le paiement requis a été effectué
        // avant de proposer le passage à "en_cours".
        $demandes->getCollection()->transform(function ($demande) {
            $demande->montant_deja_paye = Paiement::where('demande_id', $demande->id)->sum('montant_paye');
            $demande->paiement_requis = $demande->service && $demande->service->prix > 0;
            $demande->paiement_ok = !$demande->paiement_requis || $demande->montant_deja_paye > 0;
            return $demande;
        });

        return view('admin.demandes.index', compact('demandes', 'services', 'stats'));
    }

    // ===== DÉTAIL =====
    public function show(DemandeService $demande)
    {
        $demande->load(['service.categorie', 'user']);

        $demande->montant_deja_paye = Paiement::where('demande_id', $demande->id)->sum('montant_paye');
        $demande->paiement_requis = $demande->service && $demande->service->prix > 0;
        $demande->paiement_ok = !$demande->paiement_requis || $demande->montant_deja_paye > 0;

        return view('admin.demandes.show', compact('demande'));
    }

    // ===== CHANGER STATUT =====
    public function changerStatut(Request $request, DemandeService $demande)
    {
        $request->validate([
            'statut'  => 'required|in:en_attente,en_cours,termine,annule',
            'message' => 'nullable|string|max:500',
        ]);

        if (!$this->transitionAutorisee($demande->statut, $request->statut)) {
            return back()->with('error', "❌ Transition non autorisée : {$demande->statut} → {$request->statut}");
        }

        // ✅ RÈGLE MÉTIER : un service payant doit avoir reçu au moins un
        // paiement (partiel ou total) avant de pouvoir démarrer ("en_cours").
        // Un service gratuit (prix null/0) n'a pas cette contrainte.
        if ($request->statut === 'en_cours' && $demande->service && $demande->service->prix > 0) {
            $montantPaye = Paiement::where('demande_id', $demande->id)->sum('montant_paye');

            if ($montantPaye <= 0) {
                return back()->with('error', "❌ Impossible de démarrer ce service : le client n'a effectué aucun paiement pour la demande #{$demande->id}. Un paiement partiel ou total est requis avant de passer en \"En cours\".");
            }
        }

        $ancienStatut = $demande->statut;
        $demande->update(['statut' => $request->statut]);

        $statutsLabels = [
            'en_attente' => '⏳ En attente',
            'en_cours'   => '🔄 En cours',
            'termine'    => '✅ Terminé',
            'annule'     => '❌ Annulé',
        ];

        $messageNotif = $request->message
            ?? "Votre demande pour \"{$demande->service->titre}\" est maintenant : {$statutsLabels[$request->statut]}";

        if ($demande->user_id) {
            Notification::create([
                'user_id' => $demande->user_id,
                'titre'   => '📋 Mise à jour de votre demande',
                'message' => e($messageNotif),
                'type'    => match($request->statut) {
                    'termine'  => 'success',
                    'annule'   => 'error',
                    'en_cours' => 'info',
                    default    => 'info',
                },
            ]);
        }

        $email = $demande->user?->email ?? $demande->email_visiteur;
        $nom   = $demande->user?->nom_complet ?? $demande->nom_visiteur;

        if ($email) {
            Mail::to($email)->send(new StatutDemandeMail($demande, trim($nom), $messageNotif, $statutsLabels[$request->statut]));
        }

        return back()->with('success', "Statut mis à jour : {$statutsLabels[$ancienStatut]} → {$statutsLabels[$request->statut]}");
    }
}