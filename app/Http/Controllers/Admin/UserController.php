<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    // ===== LISTE CLIENTS =====
    public function index(Request $request)
    {
        $query = User::role('client')->with(['inscriptions.formation']);

        // Filtre statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre formation
        if ($request->filled('formation_id')) {
            $query->whereHas('inscriptions', function ($q) use ($request) {
                $q->where('formation_id', $request->formation_id);
            });
        }

        // Filtre date
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // Recherche nom/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients    = $query->latest()->paginate(15)->withQueryString();
        $formations = Formation::where('statut', 'publie')->get();

        return view('admin.users.index', compact('clients', 'formations'));
    }

    // ===== VUE DÉTAILLÉE =====
    public function show(User $user)
    {
        $user->load([
            'inscriptions.formation',
            'demandesService.service',
            'notifications' => fn($q) => $q->latest()->take(10),
            'temoignages',
        ]);

        $roles = $user->getRoleNames();

        return view('admin.users.show', compact('user', 'roles'));
    }

    // ===== VALIDATION INSCRIPTION =====
    public function validerInscription(InscriptionFormation $inscription)
    {
        $inscription->update(['statut' => 'valide']);

        // Notifier le client
        Notification::create([
            'user_id' => $inscription->user_id,
            'titre'   => '✅ Inscription validée !',
            'message' => "Votre inscription à la formation \"{$inscription->formation->titre}\" a été validée. Accédez aux ressources dès maintenant.",
            'type'    => 'success',
        ]);

        return back()->with('success', 'Inscription validée avec succès !');
    }

    // ===== REJET INSCRIPTION =====
    public function rejeterInscription(InscriptionFormation $inscription)
    {
        $inscription->update(['statut' => 'refuse']);

        Notification::create([
            'user_id' => $inscription->user_id,
            'titre'   => '❌ Inscription refusée',
            'message' => "Votre inscription à la formation \"{$inscription->formation->titre}\" n'a pas été acceptée. Contactez-nous pour plus d'informations.",
            'type'    => 'error',
        ]);

        return back()->with('success', 'Inscription refusée.');
    }

    // ===== SUSPENSION / RÉACTIVATION =====
    public function toggleStatut(User $user)
    {
        // Empêcher de suspendre un admin
        abort_if($user->hasRole('admin'), 403, 'Impossible de suspendre un administrateur.');

        $nouveauStatut = $user->statut === 'actif' ? 'suspendu' : 'actif';
        $user->update(['statut' => $nouveauStatut]);

        $message = $nouveauStatut === 'suspendu'
            ? "Votre compte a été suspendu. Contactez l'administration."
            : "Votre compte a été réactivé. Bienvenue de retour !";

        Notification::create([
            'user_id' => $user->id,
            'titre'   => $nouveauStatut === 'suspendu' ? '⛔ Compte suspendu' : '✅ Compte réactivé',
            'message' => $message,
            'type'    => $nouveauStatut === 'suspendu' ? 'warning' : 'success',
        ]);

        return back()->with('success',
            "Compte " . ($nouveauStatut === 'suspendu' ? 'suspendu' : 'réactivé') . " avec succès."
        );
    }

    // ===== LISTE ENSEIGNANTS =====
    public function enseignants()
    {
        $enseignants = User::role('enseignant')
            ->withCount('ressources')
            ->latest()->get();

        $formations = Formation::where('statut', 'publie')->get();

        return view('admin.users.enseignants', compact('enseignants', 'formations'));
    }

    // ===== AJOUTER ENSEIGNANT =====
    public function storeEnseignant(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'telephone' => 'nullable|string|max:20',
            'password'  => 'required|min:8|confirmed',
        ]);

        $enseignant = User::create([
            'nom'               => $request->nom,
            'prenom'            => $request->prenom,
            'email'             => $request->email,
            'telephone'         => $request->telephone,
            'password'          => Hash::make($request->password),
            'statut'            => 'actif',
            'email_verified_at' => now(),
        ]);

        $enseignant->assignRole('enseignant');

        return back()->with('success', "Enseignant {$enseignant->nom_complet} créé avec succès !");
    }

    // ===== MODIFIER ENSEIGNANT =====
    public function updateEnseignant(Request $request, User $user)
    {
        abort_if(!$user->hasRole('enseignant'), 403);

        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'telephone' => $request->telephone,
        ]);

        return back()->with('success', 'Enseignant mis à jour avec succès !');
    }

    // ===== SUPPRIMER UTILISATEUR =====
    public function destroy(User $user)
    {
        abort_if($user->hasRole('admin'), 403, 'Impossible de supprimer un administrateur.');
        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }
}