<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConfigurationController extends Controller
{
    public function index()
    {
        $configs = Configuration::orderBy('cle')->get();
        return view('admin.configurations', compact('configs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'upload_taille_max_mb'        => 'required|integer|min:1|max:100',
            'upload_types_autorises'      => 'required|string',
            'url_signee_expiration_minutes'=> 'required|integer|min:5|max:1440',
            'upload_image_taille_max_mb'  => 'required|integer|min:1|max:10',
        ]);

        foreach ($request->except(['_token', '_method']) as $cle => $valeur) {
            Configuration::set($cle, $valeur);
        }

        // Vider tout le cache de config
        Cache::flush();

        return back()->with('success', '✅ Configurations sauvegardées !');
    }
}