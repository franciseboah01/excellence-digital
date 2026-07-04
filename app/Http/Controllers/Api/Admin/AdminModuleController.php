<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminModuleController extends Controller
{
    public function index()
    {
        $modules = Module::withCount('formations')->get();
        return response()->json($modules);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:modules',
            'slug' => 'nullable|string|unique:modules',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['nom']);

        $module = Module::create($validated);

        return response()->json([
            'message' => 'Module créé avec succès.',
            'module' => $module
        ], 201);
    }

    public function update(Module $module, Request $request)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255|unique:modules,nom,' . $module->id,
            'slug' => 'nullable|string|unique:modules,slug,' . $module->id,
            'description' => 'nullable|string',
        ]);

        if (isset($validated['nom']) && !isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nom']);
        }

        $module->update($validated);

        return response()->json([
            'message' => 'Module mis à jour.',
            'module' => $module->fresh()
        ]);
    }

    public function destroy(Module $module)
    {
        if ($module->formations()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer ce module car il contient des formations.'
            ], 422);
        }

        $module->delete();

        return response()->json(['message' => 'Module supprimé.']);
    }
}