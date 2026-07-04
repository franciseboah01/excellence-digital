<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Formation;
use App\Models\Article;
use App\Models\Faq;
use App\Models\Categorie;
use App\Models\Module;
use App\Models\DemandeService;
use App\Models\Contact;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        return response()->json([
            'services_count' => Service::where('actif', true)->count(),
            'formations_count' => Formation::count(),
            'clients_count' => \App\Models\User::role('client')->count(),
            'temoignages' => \App\Models\Temoignage::where('valide', true)
                ->with('user:id,nom,prenom,avatar')
                ->latest()
                ->take(5)
                ->get(),
            'articles_recents' => Article::latest()->take(3)->get(['id', 'titre', 'slug', 'extrait', 'image', 'created_at']),
        ]);
    }

    public function services(Request $request)
    {
        $services = Service::where('actif', true)
            ->when($request->categorie_id, fn($q) => $q->where('categorie_id', $request->categorie_id))
            ->with('categorie:id,nom')
            ->paginate(12);

        return response()->json($services);
    }

    public function serviceShow(Service $service)
    {
        $service->load('categorie');
        return response()->json($service);
    }

    public function servicesByCategorie(Categorie $categorie)
    {
        $services = Service::where('categorie_id', $categorie->id)
            ->where('actif', true)
            ->paginate(12);

        return response()->json([
            'categorie' => $categorie,
            'services' => $services
        ]);
    }

    public function formations(Request $request)
    {
        $formations = Formation::with(['niveaux', 'module:id,nom'])
            ->when($request->module_id, fn($q) => $q->where('module_id', $request->module_id))
            ->paginate(12);

        return response()->json($formations);
    }

    public function formationShow(Formation $formation)
    {
        $formation->load(['niveaux', 'module', 'enseignants:id,nom,prenom,avatar']);
        return response()->json($formation);
    }

    public function formationsByModule($slug)
    {
        $module = Module::where('slug', $slug)->firstOrFail();
        $formations = Formation::where('module_id', $module->id)->paginate(12);

        return response()->json([
            'module' => $module,
            'formations' => $formations
        ]);
    }

    public function blog(Request $request)
    {
        $articles = Article::with('categorie:id,nom')
            ->when($request->categorie, fn($q) => $q->whereHas('categorie', fn($q) => $q->where('slug', $request->categorie)))
            ->latest()
            ->paginate(9);

        return response()->json($articles);
    }

    public function blogCategories()
    {
        $categories = \App\Models\CategorieArticle::has('articles')->get(['id', 'nom', 'slug']);
        return response()->json($categories);
    }

    public function articleShow(Article $article)
    {
        $article->load('categorie');
        return response()->json($article);
    }

    public function faq()
    {
        $faqs = Faq::where('actif', true)->orderBy('ordre')->get();
        return response()->json($faqs);
    }

    public function about()
    {
        return response()->json([
            'titre' => config('app.name'),
            'description' => 'Excellence Digital Center - Votre centre de formation numérique.',
            'stats' => [
                'formations' => Formation::count(),
                'apprenants' => \App\Models\User::role('client')->count(),
                'services' => Service::where('actif', true)->count(),
            ]
        ]);
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Contact::create($validated);

        return response()->json(['message' => 'Message envoyé avec succès.'], 201);
    }

    public function demandeService(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email',
            'telephone' => 'nullable|string',
            'service_id' => 'nullable|exists:services,id',
            'description' => 'required|string',
        ]);

        DemandeService::create($validated);

        return response()->json(['message' => 'Demande envoyée avec succès.'], 201);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $formations = Formation::where('titre', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->take(5)
            ->get(['id', 'titre', 'slug', 'image']);

        $services = Service::where('nom', 'LIKE', "%{$query}%")
            ->where('actif', true)
            ->take(5)
            ->get(['id', 'nom', 'description']);

        $articles = Article::where('titre', 'LIKE', "%{$query}%")
            ->take(5)
            ->get(['id', 'titre', 'slug', 'extrait', 'image']);

        return response()->json([
            'formations' => $formations,
            'services' => $services,
            'articles' => $articles,
        ]);
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('q');
        
        $results = Formation::where('titre', 'LIKE', "%{$query}%")
            ->take(5)
            ->pluck('titre');

        return response()->json($results);
    }
}