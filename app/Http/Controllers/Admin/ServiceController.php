<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Categorie;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // ===== LISTE =====
    public function index()
    {
        $services = Service::with('categorie')
            ->withCount('demandes')
            ->latest()
            ->paginate(12);

        $stats = [
            'total'    => Service::count(),
            'actifs'   => Service::where('actif', true)->count(),
            'inactifs' => Service::where('actif', false)->count(),
        ];

        return view('admin.services.index', compact('services', 'stats'));
    }

    // ===== FORMULAIRE CRÉATION =====
    public function create()
    {
        $categories = Categorie::where('actif', true)->get();
        return view('admin.services.create', compact('categories'));
    }

    // ===== ENREGISTRER =====
    public function store(Request $request)
    {
        $validated = $request->validate([
            'icone'        => 'nullable|string|max:5',
            'categorie_id' => 'required|exists:categories,id',
            'titre'        => 'required|string|max:200',
            'description'  => 'required|string|max:1000',
            'prix'         => 'nullable|integer|min:0',
        ]);

        Service::create($validated + ['actif' => true]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service créé avec succès !');
    }

    // ===== FORMULAIRE MODIFICATION =====
    public function edit(Service $service)
    {
        $categories = Categorie::where('actif', true)->get();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    // ===== METTRE À JOUR =====
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'icone'        => 'nullable|string|max:5',
            'categorie_id' => 'required|exists:categories,id',
            'titre'        => 'required|string|max:200',
            'description'  => 'required|string|max:1000',
            'prix'         => 'nullable|integer|min:0',
            'actif'        => 'boolean',
        ]);

        $service->update($validated);

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
        return back()->with('success', 'Service ' . ($service->actif ? 'activé' : 'désactivé') . '.');
    }
}