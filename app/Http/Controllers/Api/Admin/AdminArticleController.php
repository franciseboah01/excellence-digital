<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with('categorie:id,nom')
            ->when($request->categorie_id, fn($q) => $q->where('categorie_id', $request->categorie_id))
            ->when($request->search, fn($q) => $q->where('titre', 'LIKE', "%{$request->search}%"))
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->latest()
            ->paginate(15);

        return response()->json($articles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'extrait' => 'nullable|string|max:500',
            'categorie_id' => 'required|exists:categorie_articles,id',
            'image' => 'nullable|image|max:2048',
            'tags' => 'nullable|array',
            'statut' => 'in:brouillon,publie',
        ]);

        $validated['slug'] = Str::slug($validated['titre']);
        $validated['user_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        if (!isset($validated['extrait'])) {
            $validated['extrait'] = Str::limit(strip_tags($validated['contenu']), 200);
        }

        $article = Article::create($validated);

        if (isset($validated['tags'])) {
            $article->attachTags($validated['tags']);
        }

        return response()->json([
            'message' => 'Article créé avec succès.',
            'article' => $article->load('categorie')
        ], 201);
    }

    public function show(Article $article)
    {
        return response()->json($article->load(['categorie', 'tags']));
    }

    public function update(Article $article, Request $request)
    {
        $validated = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'contenu' => 'sometimes|string',
            'extrait' => 'nullable|string|max:500',
            'categorie_id' => 'sometimes|exists:categorie_articles,id',
            'image' => 'nullable|image|max:2048',
            'tags' => 'nullable|array',
            'statut' => 'in:brouillon,publie',
        ]);

        if (isset($validated['titre'])) {
            $validated['slug'] = Str::slug($validated['titre']);
        }

        if ($request->hasFile('image')) {
            if ($article->image) {
                \Storage::disk('public')->delete($article->image);
            }
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($validated);

        if (isset($validated['tags'])) {
            $article->syncTags($validated['tags']);
        }

        return response()->json([
            'message' => 'Article mis à jour.',
            'article' => $article->fresh()->load('categorie')
        ]);
    }

    public function destroy(Article $article)
    {
        if ($article->image) {
            \Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return response()->json(['message' => 'Article supprimé.']);
    }
}