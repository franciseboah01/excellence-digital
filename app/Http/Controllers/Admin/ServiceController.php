<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // ===== LISTE =====
    public function index()
    {
        $services = Service::withCount('demandes')
            ->latest()->paginate(12);

        $stats = [
            'total'       => Service::count(),
            'actifs'      => Service::where('actif', true)->count(),
            'bureautique' => Service::where('categorie', 'bureautique')->count(),
            'design'      => Service::where('categorie', 'design')->count(),
            'web_mobile'  => Service::where('categorie', 'web_mobile')->count(),
        ];

        return view('admin.services.index', compact('services', 'stats'));
    }

    // ===== FORMULAIRE CRÉATION =====
    public function create()
    {
        return view('admin.services.create');
    }

    // ===== ENREGISTRER =====
    public function store(Request $request)
    {
        $request->validate([
            'titre'       => 'required|string|max:200',
            'description' => 'required|string',
            'categorie'   => 'required|in:bureautique,design,web_mobile',
            'prix'        => 'nullable|numeric|min:0',
            'icone'       => 'nullable|string|max:10',
        ]);

        Service::create([
            'titre'       => $request->titre,
            'description' => $request->description,
            'categorie'   => $request->categorie,
            'prix'        => $request->prix,
            'icone'       => $request->icone ?? '⚙️',
            'actif'       => true,
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service créé avec succès !');
    }

    // ===== FORMULAIRE MODIFICATION =====
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    // ===== METTRE À JOUR =====
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'titre'       => 'required|string|max:200',
            'description' => 'required|string',
            'categorie'   => 'required|in:bureautique,design,web_mobile',
            'prix'        => 'nullable|numeric|min:0',
            'icone'       => 'nullable|string|max:10',
        ]);

        $service->update([
            'titre'       => $request->titre,
            'description' => $request->description,
            'categorie'   => $request->categorie,
            'prix'        => $request->prix,
            'icone'       => $request->icone,
            'actif'       => $request->boolean('actif'),
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service mis à jour !');
    }

    // ===== SUPPRIMER =====
    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Service supprimé.');
    }

    // ===== TOGGLE ACTIF =====
    public function toggleActif(Service $service)
    {
        $service->update(['actif' => !$service->actif]);
        return back()->with('success',
            'Service ' . ($service->actif ? 'désactivé' : 'activé') . '.'
        );
    }
}