<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::withCount('services')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'   => 'required|string|max:100|unique:categories,nom',
            'icone' => 'nullable|string|max:10',
        ]);

        Categorie::create([
            'nom'   => $request->nom,
            'icone' => $request->icone ?? '📂',
        ]);

        return back()->with('success', 'Catégorie créée.');
    }

    public function update(Request $request, Categorie $categorie)
    {
        $request->validate([
            'nom'   => 'required|string|max:100|unique:categories,nom,' . $categorie->id,
            'icone' => 'nullable|string|max:10',
        ]);

        $categorie->update($request->only('nom', 'icone'));

        return back()->with('success', 'Catégorie modifiée.');
    }

    public function destroy(Categorie $categorie)
    {
        if ($categorie->services()->count() > 0) {
            return back()->with('error', 'Impossible : des services utilisent cette catégorie.');
        }

        $categorie->delete();
        return back()->with('success', 'Catégorie supprimée.');
    }
}