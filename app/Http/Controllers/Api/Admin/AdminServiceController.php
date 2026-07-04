<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::with('categorie:id,nom')
            ->when($request->categorie_id, fn($q) => $q->where('categorie_id', $request->categorie_id))
            ->when($request->search, fn($q) => $q->where('nom', 'LIKE', "%{$request->search}%"))
            ->latest()
            ->paginate(15);

        return response()->json($services);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'categorie_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'prix' => 'nullable|numeric|min:0',
            'actif' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('services', 'public');
        }

        $validated['slug'] = Str::slug($validated['nom']);

        $service = Service::create($validated);

        return response()->json([
            'message' => 'Service créé avec succès.',
            'service' => $service->load('categorie')
        ], 201);
    }

    public function show(Service $service)
    {
        return response()->json($service->load(['categorie', 'demandes']));
    }

    public function update(Service $service, Request $request)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'categorie_id' => 'sometimes|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'prix' => 'nullable|numeric|min:0',
            'actif' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($service->image) {
                \Storage::disk('public')->delete($service->image);
            }
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        if (isset($validated['nom'])) {
            $validated['slug'] = Str::slug($validated['nom']);
        }

        $service->update($validated);

        return response()->json([
            'message' => 'Service mis à jour.',
            'service' => $service->fresh()->load('categorie')
        ]);
    }

    public function destroy(Service $service)
    {
        if ($service->image) {
            \Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return response()->json(['message' => 'Service supprimé.']);
    }

    public function toggleActif(Service $service)
    {
        $service->update(['actif' => !$service->actif]);

        return response()->json([
            'message' => $service->actif ? 'Service activé.' : 'Service désactivé.',
            'actif' => $service->actif
        ]);
    }
}