<?php

namespace App\Http\Middleware;

use App\Models\InscriptionFormation;
use App\Models\Ressource;
use Closure;
use Illuminate\Http\Request;

class VerifierAccesFichier
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifier la signature de l'URL
        if (!$request->hasValidSignature()) {
            abort(403, 'Lien expiré ou invalide. Veuillez régénérer le lien depuis votre espace.');
        }

        // Vérifier authentification
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder aux ressources.');
        }

        $user = auth()->user();

        // Admin et enseignant ont accès à tout
        if ($user->hasRole('admin') || $user->hasRole('enseignant')) {
            return $next($request);
        }

        // Récupérer la ressource demandée
        $ressource = $request->route('ressource');
        if (!$ressource instanceof Ressource) {
            $ressource = Ressource::find($request->route('ressource'));
        }

        if (!$ressource) {
            abort(404, 'Ressource introuvable.');
        }

        // Vérifier que la ressource est active
        if (!$ressource->actif) {
            abort(403, 'Cette ressource n\'est plus disponible.');
        }

        // Vérifier inscription validée
        $inscrit = InscriptionFormation::where('user_id', $user->id)
            ->where('formation_id', $ressource->formation_id)
            ->where('statut', 'valide')
            ->exists();

        if (!$inscrit) {
            abort(403, 'Vous n\'êtes pas inscrit à cette formation ou votre inscription n\'est pas encore validée.');
        }

        return $next($request);
    }
}