<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnseignantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('enseignant')) {
            abort(403, 'Accès réservé aux enseignants.');
        }
        return $next($request);
    }
}