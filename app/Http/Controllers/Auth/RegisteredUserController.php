<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $formations = Formation::where('statut', 'publie')->get();
        return view('auth.register', compact('formations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom'          => ['required', 'string', 'max:255'],
            'prenom'       => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telephone'    => ['nullable', 'string', 'max:20'],
            'formation_id' => ['nullable', 'exists:formations,id'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'nom'      => $request->nom,
            'prenom'   => $request->prenom,
            'email'    => $request->email,
            'telephone'=> $request->telephone,
            'password' => Hash::make($request->password),
        ]);

        // Assigner le rôle client
        $user->assignRole('client');

        // Inscrire à la formation si choisie
        if ($request->formation_id) {
            InscriptionFormation::create([
                'user_id'      => $user->id,
                'formation_id' => $request->formation_id,
                'statut'       => 'en_attente',
            ]);
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('verification.notice'));
    }
}