<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->role, fn($q) => $q->role($request->role))
            ->when($request->search, fn($q) => $q->where(function($query) use ($request) {
                $query->where('nom', 'LIKE', "%{$request->search}%")
                      ->orWhere('prenom', 'LIKE', "%{$request->search}%")
                      ->orWhere('email', 'LIKE', "%{$request->search}%");
            }))
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->latest()
            ->paginate(20);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'telephone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|in:client,enseignant,admin',
            'statut' => 'in:actif,inactif',
        ]);

        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'password' => Hash::make($validated['password']),
            'statut' => $validated['statut'] ?? 'actif',
        ]);

        $user->assignRole($validated['role']);

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'user' => $user->load('roles')
        ], 201);
    }

    public function show(User $user)
    {
        $user->load(['roles', 'inscriptions.formation', 'demandesService', 'certificats']);
        
        return response()->json($user);
    }

    public function update(User $user, Request $request)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'role' => 'sometimes|in:client,enseignant,admin',
            'statut' => 'in:actif,inactif',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json([
            'message' => 'Utilisateur mis à jour.',
            'user' => $user->fresh()->load('roles')
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return response()->json([
                'message' => 'Impossible de supprimer le dernier administrateur.'
            ], 422);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé.']);
    }

    public function toggleStatut(User $user)
    {
        $user->update(['statut' => $user->statut === 'actif' ? 'inactif' : 'actif']);

        return response()->json([
            'message' => 'Statut modifié.',
            'statut' => $user->statut
        ]);
    }

    public function enseignants()
    {
        $enseignants = User::role('enseignant')
            ->withCount(['formationsEnseignant', 'ressources'])
            ->latest()
            ->get();

        return response()->json($enseignants);
    }

    public function storeEnseignant(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'telephone' => 'nullable|string',
            'password' => 'required|string|min:8',
            'specialites' => 'nullable|array',
        ]);

        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'password' => Hash::make($validated['password']),
            'statut' => 'actif',
        ]);

        $user->assignRole('enseignant');

        return response()->json([
            'message' => 'Enseignant créé avec succès.',
            'enseignant' => $user
        ], 201);
    }

    public function updateEnseignant(User $user, Request $request)
    {
        if (!$user->hasRole('enseignant')) {
            return response()->json(['message' => 'Cet utilisateur n\'est pas un enseignant.'], 422);
        }

        return $this->update($user, $request);
    }
}