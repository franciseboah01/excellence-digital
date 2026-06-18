<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Formation;
use App\Models\Service;
use App\Models\Temoignage;

class HomeController extends Controller
{
    public function index()
    {
        // ═══════════════════════════════════════
        // SERVICES
        // ═══════════════════════════════════════
        $services = Service::where('actif', true)
            ->take(6)
            ->get();

        // ═══════════════════════════════════════
        // FORMATIONS
        // ═══════════════════════════════════════
        $formations = Formation::where('statut', 'publie')
            ->with('module')
            ->withCount('inscriptions')
            ->latest()
            ->take(3) // 🆕 Limiter à 3 sur la home
            ->get();

        // ═══════════════════════════════════════
        // TÉMOIGNAGES
        // ═══════════════════════════════════════
        $temoignages = Temoignage::where('statut_validation', 'valide')
            ->with('user')
            ->latest()
            ->take(4)
            ->get();

        // ═══════════════════════════════════════
        // 🆕 STATS (depuis Configuration)
        // ═══════════════════════════════════════
        $stats = json_decode(Configuration::get('site_stats', '[]'), true) ?: [];

        // ═══════════════════════════════════════
        // 🆕 GALERIE (depuis Configuration)
        // ═══════════════════════════════════════
        $galeries = json_decode(Configuration::get('site_galeries', '[]'), true) ?: [];

        // ═══════════════════════════════════════
        // 🆕 POURQUOI NOUS (depuis Configuration)
        // ═══════════════════════════════════════
        $pourquoiNous = json_decode(Configuration::get('site_pourquoi_nous', '[]'), true) ?: [];

        return view('public.home', compact(
            'services',
            'formations',
            'temoignages',
            'stats',        // 🆕
            'galeries',     // 🆕
            'pourquoiNous'  // 🆕
        ));
    }
}