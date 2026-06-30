<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandeService;
use App\Models\Formation;
use App\Models\Notification;
use App\Models\Paiement;
use App\Models\Service;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Http\Requests\StorePaiementRequest;
use App\Models\DemandeDuplicata;  // ← AJOUTER
use App\Models\Certificat;        // ← AJOUTER

class PaiementController extends Controller
{
    // ===== LISTE =====
    public function index(Request $request)
    {
        $query = Paiement::with(['user', 'formation', 'service', 'enregistrePar']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('mode_paiement')) {
            $query->where('mode_paiement', $request->mode_paiement);
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
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($qu) =>
                      $qu->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%")
                  );
            });
        }

        $paiements = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'       => Paiement::count(),
            'en_attente'  => Paiement::where('statut', 'en_attente')->count(),
            'partiel'     => Paiement::where('statut', 'partiel')->count(),
            'complete'    => Paiement::where('statut', 'complete')->count(),
            'total_percu' => Paiement::sum('montant_paye'),
            'total_attendu'=> Paiement::sum('montant_total'),
        ];

        $clients = User::role('client')->orderBy('nom')->get();

        return view('admin.paiements.index', compact('paiements', 'stats', 'clients'));
    }

    // ===== FORMULAIRE CRÉATION =====
    public function create()
    {
        $clients    = User::role('client')->where('statut', 'actif')->orderBy('nom')->get();
        $formations = Formation::where('statut', 'publie')->get();
        $services   = Service::where('actif', true)->get();
        $demandes   = DemandeService::whereIn('statut', ['en_cours', 'termine'])
                        ->with(['user', 'service'])->get();

        return view('admin.paiements.create', compact(
            'clients', 'formations', 'services', 'demandes'
        ));
    }

    // ===== ENREGISTRER =====
    public function store(StorePaiementRequest $request)
    {
        $montantPaye = min($request->montant_paye, $request->montant_total);

        $statut = 'en_attente';
        if ($montantPaye >= $request->montant_total) {
            $statut = 'complete';
        } elseif ($montantPaye > 0) {
            $statut = 'partiel';
        }

        $paiement = Paiement::create([
            'user_id'       => $request->user_id,
            'formation_id'  => $request->formation_id,
            'service_id'    => $request->service_id,
            'demande_id'    => $request->demande_id,
            'montant_total' => $request->montant_total,
            'montant_paye'  => $montantPaye,
            'statut'        => $statut,
            'mode_paiement' => $request->mode_paiement,
            'reference'     => Paiement::genererReference(),
            'notes'         => $request->notes,
            'enregistre_par'=> auth()->id(),
            'date_paiement' => $request->date_paiement ?? now(),
            'certificat_id' => $request->certificat_id ?? null,  // ← AJOUTER
            'type'          => $request->type ?? 'formation',     // ← AJOUTER
        ]);

        // ===== SI PAIEMENT POUR DUPLICATA =====
        if ($request->type === 'duplicata' && $request->certificat_id) {
            $certificat = Certificat::find($request->certificat_id);
            
            if ($certificat && $statut === 'complete') {
                // Créer la demande de duplicata avec statut 'paye'
                $demande = DemandeDuplicata::create([
                    'certificat_id' => $certificat->id,
                    'user_id' => $request->user_id,
                    'paiement_id' => $paiement->id,
                    'statut' => 'paye',  // ✅ Statut payé
                    'paye' => true,
                    'montant_paye' => $montantPaye,
                ]);

                // Notifier l'admin
                Notification::create([
                    'user_id' => 1, // Admin
                    'titre' => '💰 Demande de duplicata payée',
                    'message' => User::find($request->user_id)?->prenom . ' ' . User::find($request->user_id)?->nom . ' a payé un duplicata pour ' . $certificat->formation?->titre,
                    'type' => 'info',
                    'lien' => route('admin.duplicatas.demandes'),
                ]);

                // Notifier le client
                Notification::create([
                    'user_id' => $request->user_id,
                    'titre' => '✅ Paiement du duplicata confirmé',
                    'message' => 'Votre paiement pour le duplicata a été confirmé. En attente de validation par l\'administration.',
                    'type' => 'success',
                ]);

                return redirect()->route('client.certificats.index')
                    ->with('success', '✅ Paiement effectué ! Demande de duplicata en attente de validation.');
            }
        }

        // ===== NOTIFICATION STANDARD =====
        $client = User::find($request->user_id);
        $sujet  = match($statut) {
            'complete' => "✅ Paiement complété — {$paiement->reference}",
            'partiel'  => "⚠️ Paiement partiel reçu — {$paiement->reference}",
            default    => "📋 Paiement enregistré — {$paiement->reference}",
        };

        Notification::create([
            'user_id' => $client->id,
            'titre'   => $sujet,
            'message' => "Montant payé : " . number_format($montantPaye, 0, ',', ' ') .
                         " FCFA / " . number_format($request->montant_total, 0, ',', ' ') . " FCFA.",
            'type'    => $statut === 'complete' ? 'success' : 'info',
        ]);

        return redirect()->route('admin.paiements.show', $paiement)
            ->with('success', "Paiement {$paiement->reference} enregistré !");
    }

    // ===== DÉTAIL =====
    public function show(Paiement $paiement)
    {
        $paiement->load(['user', 'formation', 'service', 'demande', 'enregistrePar']);
        return view('admin.paiements.show', compact('paiement'));
    }

    // ===== METTRE À JOUR PAIEMENT =====
    public function update(Request $request, Paiement $paiement)
    {
        $request->validate([
            'montant_paye'  => 'required|numeric|min:0|max:' . $paiement->montant_total,
            'mode_paiement' => 'required|in:especes,mobile_money,virement,autre',
            'notes'         => 'nullable|string|max:500',
        ]);

        $montantPaye = $request->montant_paye;
        $statut = 'en_attente';
        if ($montantPaye >= $paiement->montant_total) {
            $statut = 'complete';
        } elseif ($montantPaye > 0) {
            $statut = 'partiel';
        }

        $paiement->update([
            'montant_paye'   => $montantPaye,
            'statut'         => $statut,
            'mode_paiement'  => $request->mode_paiement,
            'notes'          => $request->notes,
            'enregistre_par' => auth()->id(),
            'date_paiement'  => now(),
        ]);

        // Notifier si complété
        if ($statut === 'complete') {
            Notification::create([
                'user_id' => $paiement->user_id,
                'titre'   => '✅ Paiement complété !',
                'message' => "Votre paiement {$paiement->reference} est maintenant complet. Merci !",
                'type'    => 'success',
            ]);
        }

        return back()->with('success', 'Paiement mis à jour !');
    }

    // ===== GÉNÉRER REÇU PDF =====
    public function recu(Paiement $paiement)
    {
        $paiement->load(['user', 'formation', 'service', 'enregistrePar']);

        $pdf = Pdf::loadView('pdf.recu-paiement', compact('paiement'))
            ->setPaper('a5', 'portrait');

        return $pdf->download("recu-{$paiement->reference}.pdf");
    }

    // ===== HISTORIQUE CLIENT =====
    public function historique(User $user)
    {
        $paiements = Paiement::where('user_id', $user->id)
            ->with(['formation', 'service'])
            ->latest()->get();

        $totalPaye = $paiements->sum('montant_paye');
        $totalDu   = $paiements->sum('montant_total');

        return view('admin.paiements.historique', compact('user', 'paiements', 'totalPaye', 'totalDu'));
    }
}