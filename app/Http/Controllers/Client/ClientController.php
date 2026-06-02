<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DemandeService;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\Notification;
use App\Models\Ressource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    // ===== DASHBOARD =====
    public function dashboard()
    {
        $user = auth()->user();

        $stats = [
            'demandes'        => DemandeService::where('user_id', $user->id)->count(),
            'formations'      => InscriptionFormation::where('user_id', $user->id)->count(),
            'notifications'   => Notification::where('user_id', $user->id)->where('lu', false)->count(),
            'demandes_cours'  => DemandeService::where('user_id', $user->id)
                                    ->where('statut', 'en_cours')->count(),
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
        // Vérifier que le client est bien inscrit
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

        // Ressources sans niveau
        $ressources_generales = Ressource::where('formation_id', $formation->id)
            ->whereNull('niveau_id')
            ->where('actif', true)
            ->get();

        return view('client.ressources', compact('formation', 'niveaux', 'ressources_generales'));
    }

    // ===== VISUALISER UN PDF =====
    public function voirPdf(Ressource $ressource)
    {
        // Vérifier accès
        $inscription = InscriptionFormation::where('user_id', auth()->id())
            ->where('formation_id', $ressource->formation_id)
            ->where('statut', 'valide')
            ->firstOrFail();

        if (!$ressource->fichier_path || !Storage::exists($ressource->fichier_path)) {
            abort(404, 'Fichier introuvable.');
        }

        return response()->file(Storage::path($ressource->fichier_path), [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $ressource->titre . '.pdf"',
        ]);
    }

    // ===== NOTIFICATIONS =====
    public function notifications()
    {
        // Marquer toutes comme lues
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

    public function profilUpdate(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload avatar
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
            'current_password'      => 'required',
            'password'              => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès !');
    }
}