<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use Illuminate\Support\Str;

class FormationController extends Controller
{
    public function index()
    {
        $formations = Formation::where('statut', 'publie')
            ->with('module')
            ->withCount('inscriptions')
            ->latest()
            ->get()
            ->groupBy(function ($formation) {
                return $formation->module
                    ? $formation->module->icone . ' ' . $formation->module->nom
                    : '📚 Autres formations';
            })
            ->map(function ($groupe) {
                return $groupe->take(4);
            });

        // Trier les modules par ordre alphabétique (Excel avant Word)
        $formations = $formations->sortKeys();

        // Déplacer "Autres formations" à la fin
        if (isset($formations['📚 Autres formations'])) {
            $autres = $formations->pull('📚 Autres formations');
            $formations->put('📚 Autres formations', $autres);
        }

        return view('public.formations', compact('formations'));
    }

    public function show(Formation $formation)
    {
        $niveaux = $formation->niveaux()->orderBy('ordre')->get();
        return view('public.formation-detail', compact('formation', 'niveaux'));
    }

    public function module($slug)
    {
        $formations = Formation::where('statut', 'publie')
            ->with('module')
            ->withCount('inscriptions')
            ->latest()
            ->get()
            ->filter(function ($formation) use ($slug) {
                $nomModule = $formation->module
                    ? Str::slug($formation->module->icone . ' ' . $formation->module->nom)
                    : 'autres-formations';
                return $nomModule === $slug;
            });

        $moduleNom = $formations->first()?->module
            ? $formations->first()->module->icone . ' ' . $formations->first()->module->nom
            : 'Formations';

        return view('public.formations-module', compact('formations', 'moduleNom', 'slug'));
    }
}