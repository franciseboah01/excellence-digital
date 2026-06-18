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
        public function update(Request $request)
    {
        // Récupérer la taille max pour la validation
        $maxUploadSize = $request->input('upload_image_taille_max_mb', 2);

        // ===== VALIDATION =====
        $validated = $request->validate([

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // Onglet 0 : 🏢 Identité Institutionnelle & Coordonnées
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            'site_nom'          => 'required|string|max:150',
            'site_slogan'       => 'required|string|max:255',
            'site_description'  => 'required|string|max:500',
            'site_devise'       => 'required|string|max:150',
            'site_adresse'      => 'required|string|max:255',
            'site_ville'        => 'required|string|max:100',
            'site_pays'         => 'required|string|max:100',
            'site_contact'      => 'required|string|max:50',
            'site_whatsapp'     => 'required|string|max:20',
            'site_email'        => 'required|email|max:100',
            'site_web'          => 'required|string|max:100',
            'site_copyright'    => 'required|string|max:255',

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // Onglet 1 : 📁 Stockage & Fichiers
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            'upload_taille_max_mb'       => 'required|integer|min:1|max:100',
            'upload_types_autorises'     => 'required|string|max:255',
            'upload_image_taille_max_mb' => 'required|integer|min:1|max:10',

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // Onglet 2 : 🔐 Sécurité & QCM
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            'url_signee_expiration_minutes' => 'required|integer|min:5|max:1440',
            'qcm_note_minimale'             => 'required|integer|min:0|max:20',

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // Onglet 3 : 📜 Maquette & Certificats
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            'certificat_background_file'   => 'nullable|image|mimes:jpeg,png,jpg|max:' . ($maxUploadSize * 1024),
            'certificat_font_color_name'   => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'certificat_axis_x_numero'     => 'required|integer|min:0|max:2000',
            'certificat_axis_y_numero'     => 'required|integer|min:0|max:2000',
            'certificat_font_size_numero'  => 'required|integer|min:8|max:40',
            'certificat_axis_x_name'       => 'required|integer|min:0|max:2000',
            'certificat_axis_y_name'       => 'required|integer|min:0|max:2000',
            'certificat_font_size_name'    => 'required|integer|min:8|max:72',
            'certificat_axis_x_formation'  => 'required|integer|min:0|max:2000',
            'certificat_axis_y_formation'  => 'required|integer|min:0|max:2000',
            'certificat_font_size_formation' => 'required|integer|min:8|max:50',
            'certificat_axis_x_performance' => 'required|integer|min:0|max:2000',
            'certificat_axis_y_performance' => 'required|integer|min:0|max:2000',
            'certificat_font_size_perf'    => 'required|integer|min:8|max:30',
            'certificat_axis_x_metadata'   => 'required|integer|min:0|max:2000',
            'certificat_axis_y_metadata'   => 'required|integer|min:0|max:2000',
            'certificat_qr_size'           => 'required|integer|min:40|max:200',
            'certificat_duplicata_active'  => 'nullable|boolean',
            'certificat_show_note'         => 'nullable|boolean',
            'certificat_show_mention'      => 'nullable|boolean',
            'certificat_show_qrcode'       => 'nullable|boolean',
            'duplicata_prix'               => 'required|integer|min:500|max:10000',
            'duplicata_delai_jours'        => 'required|integer|min:1|max:30',

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // Onglet 4 : 📊 Stats & Arguments
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            'site_stats'         => 'nullable|string',
            'site_pourquoi_nous' => 'nullable|string',

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // Onglet 5 : 🖼️ Galerie
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            'site_galeries'      => 'nullable|string',
            'galerie_files'      => 'nullable',
            'galerie_files.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

        ]);

        // ===== 1. GESTION DE L'IMAGE DE FOND =====
        if ($request->hasFile('certificat_background_file')) {
            $this->gererImageFond($request->file('certificat_background_file'));
        }

        // ===== 2. GESTION DES TOGGLES (Checkbox) =====
        $toggles = [
            'certificat_duplicata_active',
            'certificat_show_note',
            'certificat_show_mention',
            'certificat_show_qrcode'
        ];
        foreach ($toggles as $toggle) {
            Configuration::set($toggle, $request->has($toggle) ? 1 : 0);
        }

        // ===== 3. GESTION DES AUTRES CHAMPS =====
        $champsExclure = array_merge(['_token', '_method', 'certificat_background_file', 'galerie_files'], $toggles);
        foreach ($request->except($champsExclure) as $cle => $valeur) {
            if (!in_array($cle, $toggles)) {
                Configuration::set($cle, $valeur);
            }
        }

        // ===== 4. GESTION UPLOAD GALERIE =====
        if ($request->hasFile('galerie_files')) {
            $galeries = [];
            
            foreach ($request->file('galerie_files') as $file) {
                $path = $file->store('galerie', 'public');
                $galeries[] = ['titre' => '', 'image' => $path];
            }
            
            Configuration::set('site_galeries', json_encode($galeries));
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