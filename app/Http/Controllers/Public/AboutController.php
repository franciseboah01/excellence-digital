<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Equipe;

class AboutController extends Controller
{
    public function index()
    {
        $mission    = json_decode(Configuration::get('site_mission', '[]'), true) ?: [];
        $valeurs    = json_decode(Configuration::get('site_valeurs', '[]'), true) ?: [];
        $mapsEmbed  = Configuration::get('site_maps_embed', '');
        $equipes    = Equipe::where('actif', true)->orderBy('ordre')->get();

        return view('public.about', compact('mission', 'valeurs', 'mapsEmbed', 'equipes'));
    }
}