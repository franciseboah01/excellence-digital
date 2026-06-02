<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClientMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('client')) {
            abort(403, 'Accès réservé aux clients inscrits.');
        }
        return $next($request);
    }
}