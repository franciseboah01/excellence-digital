<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Temoignage;

class TemoignageController extends Controller
{
    public function index()
    {
        $temoignages = Temoignage::with(['user', 'formation', 'service'])
            ->latest()->paginate(20);

        $stats = [
            'total'      => Temoignage::count(),
            'en_attente' => Temoignage::where('statut_validation', 'en_attente')->count(),
            'valides'    => Temoignage::where('statut_validation', 'valide')->count(),
            'refuses'    => Temoignage::where('statut_validation', 'refuse')->count(),
        ];

        return view('admin.temoignages', compact('temoignages', 'stats'));
    }

    public function valider(Temoignage $temoignage)
    {
        $temoignage->update(['statut_validation' => 'valide']);
        return back()->with('success', 'Témoignage publié !');
    }

    public function refuser(Temoignage $temoignage)
    {
        $temoignage->update(['statut_validation' => 'refuse']);
        return back()->with('success', 'Témoignage refusé.');
    }

    public function destroy(Temoignage $temoignage)
    {
        $temoignage->delete();
        return back()->with('success', 'Témoignage supprimé.');
    }
}