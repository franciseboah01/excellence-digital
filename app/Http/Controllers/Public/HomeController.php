<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Service;
use App\Models\Temoignage;

class HomeController extends Controller
{
    public function index()
    {
        $services   = Service::where('actif', true)->take(6)->get();
        $formations = Formation::where('statut', 'publie')->take(3)->get();
        $temoignages = Temoignage::where('statut_validation', 'valide')
                        ->latest()->take(4)->get();

        return view('public.home', compact('services', 'formations', 'temoignages'));
    }
}