<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Formation;

class FormationController extends Controller
{
    public function index()
    {
        $formations = Formation::where('statut', 'publie')
                        ->withCount('inscriptions')
                        ->get();
        return view('public.formations', compact('formations'));
    }

    public function show(Formation $formation)
    {
        $niveaux = $formation->niveaux()->orderBy('ordre')->get();
        return view('public.formation-detail', compact('formation', 'niveaux'));
    }
}