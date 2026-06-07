<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Formation;
use App\Models\Service;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $query = substr(trim($request->q), 0, 100);

        $services = Service::where('actif', true)
            ->where(function ($q) use ($query) {
                $q->where('titre', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })->take(5)->get();

        $formations = Formation::where('statut', 'publie')
            ->where(function ($q) use ($query) {
                $q->where('titre', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })->take(5)->get();

        $articles = Article::where('statut', 'publie')
            ->where(function ($q) use ($query) {
                $q->where('titre', 'like', "%{$query}%")
                  ->orWhere('extrait', 'like', "%{$query}%")
                  ->orWhere('contenu', 'like', "%{$query}%");
            })->take(5)->get();

        $totalResultats = $services->count() + $formations->count() + $articles->count();

        return view('public.recherche', compact(
            'query', 'services', 'formations', 'articles', 'totalResultats'
        ));
    }

    // ===== AJAX autocomplete =====
    public function autocomplete(Request $request)
    {
        $q = substr(trim($request->get('q', '')), 0, 50);

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $resultats = [];

        Service::where('actif', true)->where('titre', 'like', "%{$q}%")
            ->take(3)->get()
            ->each(fn($s) => $resultats[] = [
                'label' => $s->titre,
                'type'  => 'service',
                'url'   => route('services.show', $s),
            ]);

        Formation::where('statut', 'publie')->where('titre', 'like', "%{$q}%")
            ->take(3)->get()
            ->each(fn($f) => $resultats[] = [
                'label' => $f->titre,
                'type'  => 'formation',
                'url'   => route('formations.show', $f),
            ]);

        Article::where('statut', 'publie')->where('titre', 'like', "%{$q}%")
            ->take(2)->get()
            ->each(fn($a) => $resultats[] = [
                'label' => $a->titre,
                'type'  => 'article',
                'url'   => route('blog.show', $a->slug),
            ]);

        return response()->json($resultats);
    }
}