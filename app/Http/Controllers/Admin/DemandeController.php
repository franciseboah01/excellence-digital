<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StatutDemandeMail;
use App\Models\DemandeService;
use App\Models\Notification;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DemandeController extends Controller
{
    // ===== LISTE =====
    public function index(Request $request)
    {
        $query = DemandeService::with(['service', 'user']);

        // Filtre statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre service
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        // Filtre période
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // Recherche nom/email
        // FIX 8 : Limiter la recherche à 50 caractères
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

        return view('admin.demandes.index', compact('demandes', 'services', 'stats'));
    }

    // ===== DÉTAIL =====
    public function show(DemandeService $demande)
    {
        $demande->load(['service', 'user']);
        return view('admin.demandes.show', compact('demande'));
    }

    // ===== CHANGER STATUT =====
    /**
    * FIX 10 : Transitions d'états autorisées
    */
    private array $transitionsAutorisees = [
        'en_attente' => ['en_cours', 'annule'],
        'en_cours'   => ['termine', 'annule'],
        'termine'    => [],   // État final
        'annule'     => [],   // État final
    ];

    private function transitionAutorisee(string $actuel, string $nouveau): bool
    {
        return in_array($nouveau, $this->transitionsAutorisees[$actuel] ?? []);
    }

    // Modifie changerStatut() :
    public function changerStatut(Request $request, DemandeService $demande)
    {
        $request->validate([
            'statut'  => 'required|in:en_attente,en_cours,termine,annule',
            'message' => 'nullable|string|max:500',
        ]);

        // FIX 10 : Vérifier la transition
        if (!$this->transitionAutorisee($demande->statut, $request->statut)) {
            return back()->with('error',
                "❌ Transition non autorisée : {$demande->statut} → {$request->statut}"
            );
        }

        $ancienStatut = $demande->statut;
        $demande->update(['statut' => $request->statut]);

        $statutsLabels = [
            'en_attente' => '⏳ En attente',
            'en_cours'   => '🔄 En cours de traitement',
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

        // FIX 4 : Toujours utiliser l'email disponible (user inscrit ou visiteur)
        $email = $demande->user?->email ?? $demande->email_visiteur;
        $nom   = $demande->user?->prenom . ' ' . $demande->user?->nom
                ?? $demande->nom_visiteur;

        if ($email) {
            Mail::to($email)->send(new StatutDemandeMail(
                $demande, trim($nom), $messageNotif, $statutsLabels[$request->statut]
            ));
        }

        return back()->with('success',
            "Statut mis à jour : {$statutsLabels[$ancienStatut]} → {$statutsLabels[$request->statut]}"
        );
    }
}