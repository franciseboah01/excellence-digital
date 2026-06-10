<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DemandeService;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\Notification;
use App\Models\Paiement;
use App\Models\Ressource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateProfilRequest;

class ClientController extends Controller
{
    // ===== DASHBOARD =====
    public function dashboard()
    {
        $user = auth()->user();

        $stats = [
            'demandes_total'   => DemandeService::where('user_id', $user->id)->count(),
            'demandes_attente' => DemandeService::where('user_id', $user->id)
                                    ->where('statut', 'en_attente')->count(),
            'demandes_cours'   => DemandeService::where('user_id', $user->id)
                                    ->where('statut', 'en_cours')->count(),
            'demandes_termine' => DemandeService::where('user_id', $user->id)
                                    ->where('statut', 'termine')->count(),
            'demandes_annule'  => DemandeService::where('user_id', $user->id)
                                    ->where('statut', 'annule')->count(),
            'formations'       => InscriptionFormation::where('user_id', $user->id)->count(),
            'notifications'    => Notification::where('user_id', $user->id)->where('lu', false)->count(),
        ];

        $dernieres_demandes = DemandeService::with('service')
            ->where('user_id', $user->id)
            ->latest()->take(3)->get();

        $mes_formations = InscriptionFormation::with('formation')
            ->where('user_id', $user->id)
            ->where('statut', 'valide')
            ->latest()->take(3)->get();

        $notifications = Notification::where('user_id', $user->id)
            ->latest()->take(5)->get();

        return view('client.dashboard', compact(
            'stats', 'dernieres_demandes',
            'mes_formations', 'notifications'
        ));
    }

    // ===== DEMANDE DE SERVICE (NOUVEAU) =====
    public function demandeForm()
    {
        $services = Service::where('actif', true)->get();
        return view('client.nouvelle-demande', compact('services'));
    }

    public function demandeStore(Request $request)
    {
        $validated = $request->validate([
            'service_id'         => 'required|exists:services,id',
            'telephone_visiteur' => 'nullable|string|max:20',
            'message'            => 'nullable|string|max:2000',
        ]);

        DemandeService::create([
            'user_id'            => auth()->id(),
            'service_id'         => $validated['service_id'],
            'nom_visiteur'       => auth()->user()->nom_complet,
            'email_visiteur'     => auth()->user()->email,
            'telephone_visiteur' => $validated['telephone_visiteur'] ?? auth()->user()->telephone,
            'message'            => $validated['message'] ?? null,
            'statut'             => 'en_attente',
        ]);

        return redirect()->route('client.demandes')
            ->with('success', 'Votre demande a été envoyée avec succès !');
    }

    // ===== MES DEMANDES =====
    public function demandes()
    {
        $demandes = DemandeService::with('service')
            ->where('user_id', auth()->id())
            ->latest()->paginate(10);

        return view('client.demandes', compact('demandes'));
    }

    // ===== MES FORMATIONS =====
    public function formations()
    {
        $inscriptions = InscriptionFormation::with(['formation.niveaux'])
            ->where('user_id', auth()->id())
            ->get();

        return view('client.formations', compact('inscriptions'));
    }

    // ===== RESSOURCES D'UNE FORMATION =====
    public function ressources(Formation $formation)
    {
        $inscription = InscriptionFormation::where('user_id', auth()->id())
            ->where('formation_id', $formation->id)
            ->where('statut', 'valide')
            ->firstOrFail();

        $niveaux = $formation->niveaux()
            ->with(['ressources' => function($q) {
                $q->where('actif', true)->orderBy('created_at', 'desc');
            }])
            ->orderBy('ordre')
            ->get();

        $ressources_generales = Ressource::where('formation_id', $formation->id)
            ->whereNull('niveau_id')
            ->where('actif', true)
            ->get();

        return view('client.ressources', compact('formation', 'niveaux', 'ressources_generales'));
    }

    // ===== VISUALISER UN PDF =====
    public function voirPdf(Ressource $ressource)
    {
        $inscription = InscriptionFormation::where('user_id', auth()->id())
            ->where('formation_id', $ressource->formation_id)
            ->where('statut', 'valide')
            ->firstOrFail();

        if (!$ressource->fichier_path) {
            return back()->with('error', 'Aucun fichier associé à cette ressource.');
        }

        if (!Storage::exists($ressource->fichier_path)) {
            return back()->with('error', 'Le fichier est introuvable sur le serveur. Contactez un enseignant.');
        }

        return response()->file(Storage::path($ressource->fichier_path), [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $ressource->titre . '.pdf"',
        ]);
    }

    // ===== NOTIFICATIONS =====
    public function notifications()
    {
        Notification::where('user_id', auth()->id())
            ->where('lu', false)
            ->update(['lu' => true]);

        $notifications = Notification::where('user_id', auth()->id())
            ->latest()->paginate(15);

        return view('client.notifications', compact('notifications'));
    }

    // ===== PROFIL =====
    public function profil()
    {
        $formations_disponibles = Formation::where('statut', 'publie')->get();
        return view('client.profil', compact('formations_disponibles'));
    }

    public function profilUpdate(UpdateProfilRequest $request)
    {
        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::delete($user->avatar);
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->update([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'telephone' => $request->telephone,
            'avatar'    => $user->avatar,
        ]);

        return back()->with('success', 'Profil mis à jour avec succès !');
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès !');
    }

    // ===== MES PAIEMENTS =====
    public function paiements()
    {
        $paiements = Paiement::where('user_id', auth()->id())
            ->with(['formation', 'service'])
            ->latest()
            ->paginate(10);

        return view('client.paiements', compact('paiements'));
    }

    // ===== FORMULAIRE DE PAIEMENT =====
    public function paiementForm(Request $request, $type, $id)
    {
        if ($type === 'formation') {
            $item = Formation::findOrFail($id);
            $montant = $item->prix ?? 0;
            $description = $item->titre;
        } elseif ($type === 'service') {
            $item = Service::findOrFail($id);
            $montant = $item->prix ?? 0;
            $description = $item->titre;
        } else {
            abort(404);
        }

        return view('client.paiement-form', compact('type', 'id', 'montant', 'description'));
    }

    // ===== TRAITEMENT DU PAIEMENT (SIMULÉ) =====
    public function paiementProcess(Request $request)
    {
        $request->validate([
            'type'          => 'required|in:formation,service',
            'id'            => 'required|integer',
            'montant'       => 'required|numeric|min:100',
            'mode_paiement' => 'required|in:orange_money,mtn_money,moov_money,visa,mastercard',
            'telephone'     => 'nullable|string|max:20',
        ]);

        // Simulation de paiement réussi
        $reference = 'EDC-PAY-' . strtoupper(uniqid());

        $paiement = Paiement::create([
            'user_id'        => auth()->id(),
            'formation_id'   => $request->type === 'formation' ? $request->id : null,
            'service_id'     => $request->type === 'service' ? $request->id : null,
            'montant_total'  => $request->montant,
            'montant_paye'   => $request->montant,
            'statut'         => 'complete',
            'mode_paiement'  => $request->mode_paiement,
            'reference'      => $reference,
            'enregistre_par' => auth()->id(),
            'date_paiement'  => now(),
            'notes'          => 'Paiement simulé — ' . $request->mode_paiement,
        ]);

        return redirect()->route('client.paiements')
            ->with('success', '✅ Paiement de ' . number_format($request->montant, 0, ',', ' ') . ' FCFA effectué avec succès ! Réf : ' . $reference);
    }
}