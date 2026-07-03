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
            // 📱 APPLICATIONS MOBILES
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            'app_android_file'    => 'nullable|file|mimes:apk|max:102400',  // 100 MB max
            'app_android_version' => 'nullable|string|max:20',
            'app_android_notes'   => 'nullable|string|max:255',
            'app_windows_file'    => 'nullable|file|mimes:exe,msi,zip|max:204800', // 200 MB max
            'app_windows_version' => 'nullable|string|max:20',
            'app_windows_notes'   => 'nullable|string|max:255',

            // ... (gardez le reste de vos validations) ...
        ]);

        // ===== GESTION DES TOGGLES =====
        $toggles = [
            'certificat_duplicata_active',
            'certificat_show_note',
            'certificat_show_mention',
            'certificat_show_qrcode',
            'app_download_active', // ✅ AJOUTÉ
        ];
        foreach ($toggles as $toggle) {
            Configuration::set($toggle, $request->has($toggle) ? 1 : 0);
        }

        // ===== 1. GESTION DE L'IMAGE DE FOND =====
        if ($request->hasFile('certificat_background_file')) {
            $this->gererImageFond($request->file('certificat_background_file'));
        }

        // ===== 2. GESTION DES CHAMPS (SIMPLES ET TABLEAUX) =====
        $champsExclure = array_merge(
            ['_token', '_method', 'certificat_background_file', 'galerie_files', 'app_android_file', 'app_windows_file'], // ✅ AJOUTÉ
            $toggles
        );

        foreach ($request->except($champsExclure) as $cle => $valeur) {
            if (in_array($cle, $toggles)) {
                continue;
            }

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

        // ===== 4. GESTION FICHIERS APPLICATIONS =====
        if ($request->hasFile('app_android_file')) {
            // Supprimer l'ancien fichier
            $ancien = Configuration::get('app_android_path');
            if ($ancien && Storage::disk('public')->exists($ancien)) {
                Storage::disk('public')->delete($ancien);
            }
            $path = $request->file('app_android_file')->store('apps', 'public');
            Configuration::set('app_android_path', $path);
        }

        if ($request->hasFile('app_windows_file')) {
            // Supprimer l'ancien fichier
            $ancien = Configuration::get('app_windows_path');
            if ($ancien && Storage::disk('public')->exists($ancien)) {
                Storage::disk('public')->delete($ancien);
            }
            $path = $request->file('app_windows_file')->store('apps', 'public');
            Configuration::set('app_windows_path', $path);
        }

        // ===== 5. VIDER LE CACHE =====
        Cache::flush();

        // ===== 6. LOG =====
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