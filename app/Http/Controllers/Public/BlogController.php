<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Faq;

class BlogController extends Controller
{
    // ===== LISTE ARTICLES =====
    public function index($categorie = null)
    {
        $query = Article::where('statut', 'publie')->with('auteur');

        // Filtre par catégorie si demandé
        if ($categorie) {
            $query->where('categorie', $categorie);
        }

        $articles = $query->latest('publie_le')->paginate(9);

        $categories = Article::where('statut', 'publie')
            ->distinct()
            ->pluck('categorie');

        $categorieActive = $categorie;

        return view('public.blog.index', compact('articles', 'categories', 'categorieActive'));
    }

    // ===== DÉTAIL ARTICLE =====
    public function show(Article $article)
    {
        abort_if($article->statut !== 'publie', 404);
        $article->incrementerVues();

        $articlesLies = Article::where('statut', 'publie')
            ->where('id', '!=', $article->id)
            ->where('categorie', $article->categorie)
            ->latest()->take(3)->get();

        return view('public.blog.show', compact('article', 'articlesLies'));
    }

    // ===== FAQ PUBLIQUE =====
    public function faq()
    {
        $faqs = Faq::where('actif', true)
            ->orderBy('categorie')
            ->orderBy('ordre')
            ->get()
            ->groupBy('categorie');

        return view('public.faq', compact('faqs'));
    }
}