<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('auteur')->latest()->paginate(12);
        $stats = [
            'total'     => Article::count(),
            'publie'    => Article::where('statut', 'publie')->count(),
            'brouillon' => Article::where('statut', 'brouillon')->count(),
            'vues'      => Article::sum('vues'),
        ];
        return view('admin.articles.index', compact('articles', 'stats'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'     => 'required|string|max:200',
            'extrait'   => 'nullable|string|max:300',
            'contenu'   => 'required|string',
            'categorie' => 'required|string|max:50',
            'statut'    => 'required|in:publie,brouillon',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
        }

        Article::create([
            'auteur_id' => auth()->id(),
            'titre'     => $request->titre,
            'slug'      => Article::genererSlug($request->titre),
            'extrait'   => $request->extrait,
            'contenu'   => $request->contenu,
            'categorie' => $request->categorie,
            'statut'    => $request->statut,
            'image'     => $imagePath,
            'publie_le' => $request->statut === 'publie' ? now() : null,
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article créé !');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'titre'     => 'required|string|max:200',
            'extrait'   => 'nullable|string|max:300',
            'contenu'   => 'required|string',
            'categorie' => 'required|string|max:50',
            'statut'    => 'required|in:publie,brouillon',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($article->image) Storage::disk('public')->delete($article->image);
            $article->image = $request->file('image')->store('articles', 'public');
        }

        $article->update([
            'titre'     => $request->titre,
            'extrait'   => $request->extrait,
            'contenu'   => $request->contenu,
            'categorie' => $request->categorie,
            'statut'    => $request->statut,
            'image'     => $article->image,
            'publie_le' => $request->statut === 'publie' && !$article->publie_le ? now() : $article->publie_le,
        ]);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article mis à jour !');
    }

    public function destroy(Article $article)
    {
        if ($article->image) Storage::disk('public')->delete($article->image);
        $article->delete();
        return back()->with('success', 'Article supprimé.');
    }
}