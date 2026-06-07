<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('actif', true)->get()->groupBy('categorie');
        return view('public.services', compact('services'));
    }

    public function show(Service $service)
    {
        return view('public.service-detail', compact('service'));
    }
}