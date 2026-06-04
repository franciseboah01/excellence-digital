<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // ===== FORMULAIRE ENVOI =====
    public function form()
    {
        $clients     = User::role('client')->where('statut', 'actif')->get();
        $enseignants = User::role('enseignant')->where('statut', 'actif')->get();
        $formations  = Formation::where('statut', 'publie')
                        ->withCount(['inscriptions as inscrits_valides' => fn($q) =>
                            $q->where('statut', 'valide')
                        ])->get();

        $historique = Notification::with('user')
                        ->whereHas('user', fn($q) => $q->whereNotNull('id'))
                        ->latest()->paginate(20);

        $stats = [
            'total'      => Notification::count(),
            'non_lues'   => Notification::where('lu', false)->count(),
            'aujourdhui' => Notification::whereDate('created_at', today())->count(),
        ];

        return view('admin.notifications', compact(
            'clients', 'enseignants', 'formations',
            'historique', 'stats'
        ));
    }

    // ===== ENVOI CIBLÉ (1 utilisateur) =====
    public function envoyerCible(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'titre'    => 'required|string|max:150',
            'message'  => 'required|string|max:500',
            'type'     => 'required|in:info,success,warning,error',
        ]);

        Notification::create([
            'user_id' => $request->user_id,
            'titre'   => e($request->titre),
            'message' => e($request->message),
            'type'    => $request->type,
            'data'    => ['expediteur' => 'Administrateur', 'expediteur_role' => 'admin'],
        ]);

        $user = User::find($request->user_id);
        return back()->with('success',
            "✅ Notification envoyée à {$user->nom_complet} !"
        );
    }

    // ===== ENVOI GROUPÉ (tous les clients d'une formation) =====
    public function envoyerGroupe(Request $request)
    {
        $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'titre'        => 'required|string|max:150',
            'message'      => 'required|string|max:500',
            'type'         => 'required|in:info,success,warning,error',
        ]);

        $formation  = Formation::findOrFail($request->formation_id);
        $apprenants = $formation->inscriptions()
                        ->where('statut', 'valide')
                        ->with('user')
                        ->get();

        $count = 0;
        foreach ($apprenants as $inscription) {
            Notification::create([
                'user_id' => $inscription->user_id,
                'titre'   => e($request->titre),
                'message' => e($request->message),
                'type'    => $request->type,
                'data'    => [
                    'expediteur'      => 'Administrateur',
                    'expediteur_role' => 'admin',
                    'formation'       => $formation->titre,
                ],
            ]);
            $count++;
        }

        return back()->with('success',
            "✅ Notification envoyée à {$count} apprenant(s) de \"{$formation->titre}\" !"
        );
    }

    // ===== ENVOI À TOUS LES CLIENTS =====
    public function envoyerTous(Request $request)
    {
        $request->validate([
            'titre'   => 'required|string|max:150',
            'message' => 'required|string|max:500',
            'type'    => 'required|in:info,success,warning,error',
            'cible'   => 'required|in:clients,enseignants,tous',
        ]);

        $query = User::query();

        if ($request->cible === 'clients') {
            $query->role('client');
        } elseif ($request->cible === 'enseignants') {
            $query->role('enseignant');
        } else {
            $query->whereHas('roles', fn($q) =>
                $q->whereIn('name', ['client', 'enseignant'])
            );
        }

        $users = $query->where('statut', 'actif')->get();
        $count = 0;

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'titre'   => e($request->titre),
                'message' => e($request->message),
                'type'    => $request->type,
                'data'    => [
                    'expediteur'      => 'Administrateur',
                    'expediteur_role' => 'admin',
                    'diffusion'       => $request->cible,
                ],
            ]);
            $count++;
        }

        return back()->with('success',
            "✅ Notification envoyée à {$count} utilisateur(s) !"
        );
    }

    // ===== SUPPRIMER UNE NOTIFICATION =====
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification supprimée.');
    }

    // ===== TOUT MARQUER COMME LU (pour un user via AJAX) =====
    public function marquerLu(Request $request)
    {
        Notification::where('user_id', auth()->id())
            ->where('lu', false)
            ->update(['lu' => true]);

        return response()->json(['success' => true]);
    }

    // ===== COMPTER NON LUES (AJAX) =====
    public function compterNonLues()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('lu', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    // ===== DERNIÈRES NOTIFS (AJAX polling) =====
    public function dernieres()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->where('lu', false)
            ->latest()
            ->take(5)
            ->get(['id', 'titre', 'message', 'type', 'created_at']);

        return response()->json($notifications);
    }
}