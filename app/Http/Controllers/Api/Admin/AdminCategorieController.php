<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class AdminCategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::withCount('services')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        $categorie = Categorie::create($validated);

        return response()->json([
            'message' => 'Catégorie créée avec succès.',
            'categorie' => $categorie
        ], 201);
    }

    public function update(Categorie $categorie, Request $request)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255|unique:categories,nom,' . $categorie->id,
            'description' => 'nullable|string',
        ]);

        $categorie->update($validated);

        return response()->json([
            'message' => 'Catégorie mise à jour.',
            'categorie' => $categorie->fresh()
        ]);
    }

    public function destroy(Categorie $categorie)
    {
        if ($categorie->services()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cette catégorie car elle contient des services.'
            ], 422);
        }

        $categorie->delete();

        return response()->json(['message' => 'Catégorie supprimée.']);
    }
}