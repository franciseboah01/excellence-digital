<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('actif', true)
            ->with('categorie')
            ->get()
            ->groupBy(function ($service) {
                return $service->categorie 
                    ? $service->categorie->icone . ' ' . $service->categorie->nom 
                    : '📂 Autres services';
            })
            ->map(function ($groupe) {
                return $groupe->sortByDesc('created_at')->take(4);
            });

        // 🆕 Déplacer "Autres services" à la fin
        if (isset($services['📂 Autres services'])) {
            $autres = $services->pull('📂 Autres services');
            $services->put('📂 Autres services', $autres);
        }

        return view('public.services', compact('services'));
    }

    public function show(Service $service)
    {
        return view('public.service-detail', compact('service'));
    }

    public function categorie($slug)
    {
        $services = Service::where('actif', true)
            ->with('categorie')
            ->get()
            ->filter(function ($service) use ($slug) {
                $nomCategorie = $service->categorie
                    ? Str::slug($service->categorie->icone . ' ' . $service->categorie->nom)
                    : 'autres-services';
                return $nomCategorie === $slug;
            });

        $categorieNom = $services->first()?->categorie
            ? $services->first()->categorie->icone . ' ' . $services->first()->categorie->nom
            : 'Services';

        return view('public.services-categorie', compact('services', 'categorieNom', 'slug'));
    }
}