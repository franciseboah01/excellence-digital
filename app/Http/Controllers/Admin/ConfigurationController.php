<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ConfigurationController extends Controller
{
    /**
     * ============================================================
     * 1. AFFICHER LES CONFIGURATIONS
     * ============================================================
     */
    public function index()
    {
        $configs = Configuration::orderBy('cle')->get();
        return view('admin.configurations', compact('configs'));
    }

        /**
         * ============================================================
         * 2. METTRE À JOUR LES CONFIGURATIONS
         * ============================================================
         */
        // Dans App\Http\Controllers\Admin\ConfigurationController.php

    public function update(Request $request)
    {
        // Récupérer la taille max pour la validation
        $maxUploadSize = $request->input('upload_image_taille_max_mb', 2);

        // ===== VALIDATION =====
        $validated = $request->validate([
            // ... (gardez vos validations existantes) ...

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // 📊 Stats & Arguments (TABLEAUX)
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            'site_stats'         => 'nullable|array',
            'site_stats.*.valeur' => 'nullable|string|max:50',
            'site_stats.*.description' => 'nullable|string|max:255',
            
            'site_pourquoi_nous' => 'nullable|array',
            'site_pourquoi_nous.*.icone' => 'nullable|string|max:10',
            'site_pourquoi_nous.*.titre' => 'nullable|string|max:100',
            'site_pourquoi_nous.*.description' => 'nullable|string|max:255',

            'site_galeries'      => 'nullable|array',
            'site_galeries.*.titre' => 'nullable|string|max:255',
            'site_galeries.*.image' => 'nullable|string|max:255',

            'site_mission'       => 'nullable|array',
            'site_mission.*.icone' => 'nullable|string|max:10',
            'site_mission.*.titre' => 'nullable|string|max:100',
            'site_mission.*.description' => 'nullable|string|max:255',

            'site_valeurs'       => 'nullable|array',
            'site_valeurs.*.icone' => 'nullable|string|max:10',
            'site_valeurs.*.titre' => 'nullable|string|max:100',
            'site_valeurs.*.description' => 'nullable|string|max:255',

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // Google Maps Embed (reste une chaîne)
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            'site_maps_embed' => 'nullable|string',

            // ... gardez le reste ...
        ]);

        // ===== GESTION DES TOGGLES =====
        $toggles = [
            'certificat_duplicata_active',
            'certificat_show_note',
            'certificat_show_mention',
            'certificat_show_qrcode'
        ];
        foreach ($toggles as $toggle) {
            Configuration::set($toggle, $request->has($toggle) ? 1 : 0);
        }

        // ===== 1. GESTION DE L'IMAGE DE FOND =====
        if ($request->hasFile('certificat_background_file')) {
            $this->gererImageFond($request->file('certificat_background_file'));
        }

        // ===== 2. GESTION DES CHAMPS (SIMPLES ET TABLEAUX) =====
        $champsExclure = array_merge(['_token', '_method', 'certificat_background_file', 'galerie_files'], $toggles);

        foreach ($request->except($champsExclure) as $cle => $valeur) {
            if (in_array($cle, $toggles)) {
                continue; // déjà traité
            }

            // SI C'EST UN TABLEAU, ON LE CONVERTIT EN JSON
            if (is_array($valeur)) {
                $valeur = json_encode($valeur);
            }

            Configuration::set($cle, $valeur);
        }

        // ===== 3. GESTION UPLOAD GALERIE =====
        if ($request->hasFile('galerie_files')) {
            $galeries = [];
            foreach ($request->file('galerie_files') as $file) {
                $path = $file->store('galerie', 'public');
                $galeries[] = ['titre' => '', 'image' => $path];
            }
            Configuration::set('site_galeries', json_encode($galeries));
        }

        // ===== 4. VIDER LE CACHE =====
        Cache::flush();

        // ===== 5. LOG =====
        Log::info('Configurations mises à jour par ' . auth()->user()->email);

        return back()->with('success', '✅ Toutes les configurations ont été mises à jour !');
    }

    /**
     * ============================================================
     * 3. RÉINITIALISER UNE CONFIGURATION À SA VALEUR PAR DÉFAUT
     * ============================================================
     */
    public function reset($cle)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        $defaults = [
            'duplicata_prix' => 1000,
            'duplicata_delai_jours' => 7,
            'certificat_show_note' => 1,
            'certificat_show_qrcode' => 1,
            'certificat_duplicata_active' => 1,
            'upload_taille_max_mb' => 10,
            'upload_image_taille_max_mb' => 2,
        ];

        if (!isset($defaults[$cle])) {
            return back()->with('error', 'Cette configuration n\'a pas de valeur par défaut définie.');
        }

        Configuration::set($cle, $defaults[$cle]);
        Cache::flush();

        return back()->with('success', '✅ Configuration "' . $cle . '" réinitialisée à ' . $defaults[$cle]);
    }

    /**
     * ============================================================
     * 4. EXPORTER LES CONFIGURATIONS (JSON)
     * ============================================================
     */
    public function export()
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        $configs = Configuration::all()->pluck('valeur', 'cle');
        
        return response()->json($configs, 200, [
            'Content-Disposition' => 'attachment; filename="configurations-' . date('Y-m-d') . '.json"'
        ]);
    }

    /**
     * ============================================================
     * 5. IMPORTER DES CONFIGURATIONS (JSON)
     * ============================================================
     */
    public function import(Request $request)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        $request->validate([
            'fichier' => 'required|file|mimes:json|max:1024',
        ]);

        $contenu = json_decode(file_get_contents($request->file('fichier')), true);

        if (!$contenu || !is_array($contenu)) {
            return back()->with('error', 'Fichier JSON invalide.');
        }

        foreach ($contenu as $cle => $valeur) {
            if (Configuration::where('cle', $cle)->exists()) {
                Configuration::set($cle, $valeur);
            }
        }

        Cache::flush();

        return back()->with('success', '✅ ' . count($contenu) . ' configurations importées avec succès.');
    }

    /**
     * ============================================================
     * 6. MÉTHODES PRIVÉES
     * ============================================================
     */

    /**
     * Gérer l'upload de l'image de fond
     */
    private function gererImageFond($file)
    {
        // Supprimer l'ancienne image
        $ancienneMaquette = Configuration::get('certificat_background');
        if ($ancienneMaquette && Storage::disk('public')->exists($ancienneMaquette)) {
            Storage::disk('public')->delete($ancienneMaquette);
        }

        // Stocker la nouvelle image
        $path = $file->store('certificats', 'public');
        
        // Sauvegarder le chemin en configuration
        Configuration::set('certificat_background', $path, "Image de fond du certificat");
    }
}