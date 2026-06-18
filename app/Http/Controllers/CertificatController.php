<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use App\Models\Configuration;
use App\Models\DemandeDuplicata;
use App\Models\Notification;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificatController extends Controller
{
    /**
     * ============================================================
     * 1. TÉLÉCHARGER (PDF / JPG / PNG)
     * ============================================================
     */
    public function telecharger(Request $request, Certificat $certificat)
    {
        $format = $request->input('format', 'pdf');
        $formatsAutorises = ['pdf', 'jpg', 'jpeg', 'png'];
        
        if (!in_array($format, $formatsAutorises)) {
            $format = 'pdf';
        }

        // ===== ADMIN =====
        if (auth()->user()->hasRole('admin')) {
            $certificat->load(['user', 'formation', 'session.qcm.niveau']);
            return $this->genererSortie($certificat, $format);
        }

        // ===== CLIENT =====
        abort_if($certificat->user_id !== auth()->id(), 403);

        $estDuplicata = str_ends_with($certificat->numero_certificat, '-DUP');

        // --- ORIGINAL ---
        if (!$estDuplicata) {
            if ($certificat->telecharge) {
                return redirect()->route('client.certificats.demande-duplicata', $certificat)
                    ->with('error', 'Vous avez déjà téléchargé ce certificat. Souhaitez-vous demander un duplicata ?');
            }
        }
        // --- DUPLICATA ---
        else {
            if ($certificat->telecharge) {
                return back()->with('error', 'Ce duplicata a déjà été téléchargé.');
            }
            if (!$certificat->delivre_le) {
                return back()->with('error', 'Ce duplicata n\'a pas encore été validé par l\'administration.');
            }
        }

        // Marquer comme téléchargé
        if (!$certificat->telecharge) {
            $certificat->update(['telecharge' => true]);
        }

        $certificat->refresh();
        $certificat->load(['user', 'formation', 'session.qcm.niveau']);

        return $this->genererSortie($certificat, $format);
    }

    /**
     * ============================================================
     * 2. APERÇU
     * ============================================================
     */
    public function apercu(Certificat $certificat)
    {
        abort_if(
            $certificat->user_id !== auth()->id()
            && !auth()->user()->hasRole('admin'),
            403
        );

        $certificat->load(['user', 'formation', 'session.qcm.niveau']);
        $certificat->mention = $this->calculerMention($certificat->note_obtenue ?? 0);

        // ✅ Récupérer TOUTES les configurations
        $backgroundPath = Configuration::get('certificat_background', 'certificats/default_bg.jpg');
        $backgroundImage = asset('storage/' . $backgroundPath);

        $positions = [
            'numero' => [
                'x' => (int) Configuration::get('certificat_axis_x_numero', 240),
                'y' => (int) Configuration::get('certificat_axis_y_numero', 20),
                'size' => (int) Configuration::get('certificat_font_size_numero', 12),
            ],
            'name' => [
                'x' => (int) Configuration::get('certificat_axis_x_name', 148),
                'y' => (int) Configuration::get('certificat_axis_y_name', 105),
                'size' => (int) Configuration::get('certificat_font_size_name', 28),
            ],
            'formation' => [
                'x' => (int) Configuration::get('certificat_axis_x_formation', 148),
                'y' => (int) Configuration::get('certificat_axis_y_formation', 135),
                'size' => (int) Configuration::get('certificat_font_size_formation', 20),
            ],
            'performance' => [
                'x' => (int) Configuration::get('certificat_axis_x_performance', 148),
                'y' => (int) Configuration::get('certificat_axis_y_performance', 155),
                'size' => (int) Configuration::get('certificat_font_size_perf', 12),
            ],
            'metadata' => [
                'x' => (int) Configuration::get('certificat_axis_x_metadata', 40),
                'y' => (int) Configuration::get('certificat_axis_y_metadata', 185),
            ],
        ];

        $fontColor = Configuration::get('certificat_font_color_name', '#FFFFFF');
        $showNote = (bool) Configuration::get('certificat_show_note', 1);
        $showMention = (bool) Configuration::get('certificat_show_mention', 1);
        $showQrCode = (bool) Configuration::get('certificat_show_qrcode', 1);
        $qrSize = (int) Configuration::get('certificat_qr_size', 100);

        // Générer le QR Code pour l'aperçu
        $qrCodeDataUri = null;
        if ($showQrCode) {
            $qrCodeDataUri = $this->genererQrCodeDataUri($certificat);
        }

        return view('client.pdf.certificat', compact(
            'certificat', 
            'positions', 
            'backgroundImage',
            'fontColor',
            'showNote',
            'showMention',
            'showQrCode',
            'qrSize',
            'qrCodeDataUri'
        ));
    }

    /**
     * ============================================================
     * 3. LISTE ADMIN
     * ============================================================
     */
    public function index()
    {
        $certificats = Certificat::with(['user', 'formation'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total'   => Certificat::count(),
            'ce_mois' => Certificat::whereMonth('created_at', now()->month)->count(),
            'moyenne' => round(Certificat::avg('note_obtenue'), 2),
            'duplicatas' => Certificat::whereNotNull('parent_id')->count(),
        ];

        return view('admin.certificats.index', compact('certificats', 'stats'));
    }

    /**
     * ============================================================
     * 4. CRÉER UN DUPLICATA (ADMIN - Direct)
     * ============================================================
     */
    public function duplicata(Certificat $certificat)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        $duplicata = $this->creerDuplicata($certificat);

        return back()->with('success', 'Duplicata créé : ' . $duplicata->numero_certificat);
    }

    /**
     * ============================================================
     * 5. VALIDER UNE DEMANDE DE DUPLICATA (ADMIN)
     * ============================================================
     */
    public function validerDuplicata(DemandeDuplicata $demande)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        if ($demande->statut !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $certificatOriginal = $demande->certificat;
        
        // Créer le duplicata
        $duplicata = $this->creerDuplicata($certificatOriginal);

        // Marquer la demande comme validée
        $demande->update([
            'statut' => 'valide',
            'valide_le' => now(),
        ]);

        // Notifier le client
        Notification::create([
            'user_id' => $demande->user_id,
            'titre'   => '✅ Duplicata disponible !',
            'message' => 'Votre duplicata pour la formation "' . ($certificatOriginal->formation->titre ?? '') . '" est disponible. Téléchargez-le maintenant.',
            'type'    => 'success',
            'lien'    => route('client.certificats.telecharger', ['certificat' => $duplicata->id, 'format' => 'pdf']),
        ]);

        return back()->with('success', 'Duplicata validé et disponible pour le client.');
    }

    /**
     * ============================================================
     * 6. REJETER UNE DEMANDE DE DUPLICATA (ADMIN)
     * ============================================================
     */
    public function rejeterDuplicata(DemandeDuplicata $demande, Request $request)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        $request->validate([
            'motif' => 'required|string|max:500',
        ]);

        $demande->update([
            'statut' => 'rejete',
            'motif_rejet' => $request->motif,
        ]);

        Notification::create([
            'user_id' => $demande->user_id,
            'titre'   => '❌ Demande de duplicata rejetée',
            'message' => 'Votre demande de duplicata a été rejetée. Motif : ' . $request->motif,
            'type'    => 'error',
        ]);

        return back()->with('success', 'Demande rejetée et client notifié.');
    }

    /**
     * ============================================================
     * 7. LISTE DES DEMANDES DE DUPLICATA (ADMIN)
     * ============================================================
     */
    public function demandesDuplicata()
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        $demandes = DemandeDuplicata::with(['certificat.formation', 'user'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => DemandeDuplicata::count(),
            'en_attente' => DemandeDuplicata::where('statut', 'en_attente')->count(),
            'valide' => DemandeDuplicata::where('statut', 'valide')->count(),
            'rejete' => DemandeDuplicata::where('statut', 'rejete')->count(),
        ];

        return view('admin.duplicatas.demandes', compact('demandes', 'stats'));
    }

    /**
     * ============================================================
     * 8. DEMANDE DE DUPLICATA (CLIENT)
     * ============================================================
     */
    public function demandeDuplicata(Certificat $certificat)
    {
        abort_if($certificat->user_id !== auth()->id(), 403);

        // Vérifier que le certificat est bien un original déjà téléchargé
        if (!$certificat->telecharge) {
            return back()->with('error', 'Vous n\'avez pas encore téléchargé ce certificat original.');
        }

        if (str_ends_with($certificat->numero_certificat, '-DUP')) {
            return back()->with('error', 'Ce certificat est déjà un duplicata.');
        }

        // Vérifier qu'il n'y a pas déjà une demande en cours
        $demandeExistante = DemandeDuplicata::where('certificat_id', $certificat->id)
            ->whereIn('statut', ['en_attente', 'valide'])
            ->exists();

        if ($demandeExistante) {
            return back()->with('error', 'Une demande de duplicata est déjà en cours pour ce certificat.');
        }

        // Vérifier si un duplicata existe déjà (non téléchargé)
        $duplicataExistant = Certificat::where('parent_id', $certificat->id)
            ->where('telecharge', false)
            ->exists();

        if ($duplicataExistant) {
            return back()->with('error', 'Un duplicata est déjà disponible. Vérifiez votre espace certificats.');
        }

        // Sauvegarde en session pour le paiement
        session(['certificat_id' => $certificat->id]);

        return redirect()->route('client.paiement.form', [
            'type' => 'service',
            'id' => 'duplicata'
        ]);
    }

    /**
     * ============================================================
     * 9. VÉRIFICATION PUBLIQUE (Jeton)
     * ============================================================
     */
    public function verification(string $token)
    {
        $certificat = Certificat::with(['user', 'formation'])
            ->where('verification_token', $token)
            ->first();

        if (!$certificat) {
            return view('public.certificats.verification', [
                'estValide' => false,
                'certificat' => null,
                'historique' => collect()
            ]);
        }

        // Déterminer la souche
        $estDuplicata = str_ends_with($certificat->numero_certificat, '-DUP');
        $soucheId = $estDuplicata ? $certificat->parent_id : $certificat->id;

        $historique = Certificat::where('id', $soucheId)
            ->orWhere('parent_id', $soucheId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('public.certificats.verification', [
            'estValide' => true,
            'certificat' => $certificat,
            'historique' => $historique
        ]);
    }

    /**
     * ============================================================
     * 10. GÉNÉRER L'IMAGE DU CERTIFICAT (JPG)
     * ============================================================
     */
    public function genererFichierCertificat(Certificat $certificat)
    {
        // 1. Image de fond
        $bgRelativePath = Configuration::get('certificat_background', 'certificats/default_bg.jpg');
        $bgRelativePath = str_replace(['..', '\\'], '', $bgRelativePath);
        $bgPath = storage_path('app/public/' . $bgRelativePath);

        if (!file_exists($bgPath)) {
            $defaultBg = storage_path('app/public/certificats/default_bg.jpg');
            $bgPath = file_exists($defaultBg) ? $defaultBg : null;
        }

        if (!$bgPath || !file_exists($bgPath)) {
            abort(404, "Image de fond de la maquette introuvable.");
        }

        $image = imagecreatefromjpeg($bgPath);

        // 2. Police
        $fontPaths = [
            storage_path('fonts/Inter-Bold.ttf'),
            storage_path('fonts/arial.ttf'),
            public_path('fonts/Inter-Bold.ttf'),
        ];
        $fontPath = null;
        foreach ($fontPaths as $path) {
            if (file_exists($path)) {
                $fontPath = $path;
                break;
            }
        }
        if (!$fontPath) {
            $fontPath = 5; // Police GD système
        }

        // 3. Couleur du texte
        $hexColor = Configuration::get('certificat_font_color_name', '#FFFFFF');
        list($r, $g, $b) = sscanf($hexColor, '#%02x%02x%02x');
        $couleurTexte = imagecolorallocate($image, $r, $g, $b);

        // 4. Positions
        $positions = [
            'numero' => [
                'x' => (int) Configuration::get('certificat_axis_x_numero', 240),
                'y' => (int) Configuration::get('certificat_axis_y_numero', 20),
                'size' => (int) Configuration::get('certificat_font_size_numero', 12),
            ],
            'name' => [
                'x' => (int) Configuration::get('certificat_axis_x_name', 148),
                'y' => (int) Configuration::get('certificat_axis_y_name', 105),
                'size' => (int) Configuration::get('certificat_font_size_name', 28),
            ],
            'formation' => [
                'x' => (int) Configuration::get('certificat_axis_x_formation', 148),
                'y' => (int) Configuration::get('certificat_axis_y_formation', 135),
                'size' => (int) Configuration::get('certificat_font_size_formation', 20),
            ],
            'performance' => [
                'x' => (int) Configuration::get('certificat_axis_x_performance', 148),
                'y' => (int) Configuration::get('certificat_axis_y_performance', 155),
                'size' => (int) Configuration::get('certificat_font_size_perf', 12),
            ],
        ];

        // 5. Écrire les textes
        $this->ecrireTexte($image, $certificat->numero_certificat, $positions['numero'], $fontPath, $couleurTexte);
        $this->ecrireTexte($image, strtoupper($certificat->user->name ?? 'Utilisateur'), $positions['name'], $fontPath, $couleurTexte);
        $this->ecrireTexte($image, $certificat->formation->titre ?? 'Formation', $positions['formation'], $fontPath, $couleurTexte);

        // 6. Performance (note + mention)
        if (Configuration::get('certificat_show_note', 1)) {
            $note = $certificat->note_obtenue ?? 0;
            $mention = $this->calculerMention($note);
            $performanceTxt = "Note : " . number_format($note, 1) . "/20 | Mention : " . $mention;
            $this->ecrireTexte($image, $performanceTxt, $positions['performance'], $fontPath, $couleurTexte);
        }

        // 7. QR Code
        if (Configuration::get('certificat_show_qrcode', 1)) {
            $this->ajouterQrCode($image, $certificat);
        }

        // 8. Sauvegarde
        $nomFichier = 'sorties/certificat_' . $certificat->verification_token . '.jpg';
        $cheminSortie = storage_path('app/public/' . $nomFichier);
        $dossier = dirname($cheminSortie);
        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }

        imagejpeg($image, $cheminSortie, 95);
        imagedestroy($image);

        return $nomFichier;
    }

    /**
     * ============================================================
     * 11. MÉTHODES PRIVÉES (Helpers)
     * ============================================================
     */

    /**
     * Générer le QR Code en Data URI pour le PDF
     */
    private function genererQrCodeDataUri(Certificat $certificat): ?string
    {
        try {
            $urlVerification = route('certificat.verification', ['token' => $certificat->verification_token]);
            $qrSize = (int) Configuration::get('certificat_qr_size', 100);

            $qrCodePngRaw = QrCode::format('png')
                ->size($qrSize)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($urlVerification);

            return 'data:image/png;base64,' . base64_encode($qrCodePngRaw);
        } catch (\Exception $e) {
            Log::error('Erreur génération QR Code Data URI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Générer la sortie du certificat (PDF, JPG ou PNG)
     */
    private function genererSortie(Certificat $certificat, string $format)
    {
        $nomFichier = 'certificat-' . $certificat->numero_certificat;

        // ===== DONNÉES COMMUNES POUR PDF ET IMAGE =====
        $backgroundPath = Configuration::get('certificat_background', 'certificats/default_bg.jpg');
        $backgroundImage = asset('storage/' . $backgroundPath);

        // Récupérer toutes les positions
        $positions = [
            'numero' => [
                'x' => (int) Configuration::get('certificat_axis_x_numero', 240),
                'y' => (int) Configuration::get('certificat_axis_y_numero', 20),
                'size' => (int) Configuration::get('certificat_font_size_numero', 12),
            ],
            'name' => [
                'x' => (int) Configuration::get('certificat_axis_x_name', 148),
                'y' => (int) Configuration::get('certificat_axis_y_name', 105),
                'size' => (int) Configuration::get('certificat_font_size_name', 28),
            ],
            'formation' => [
                'x' => (int) Configuration::get('certificat_axis_x_formation', 148),
                'y' => (int) Configuration::get('certificat_axis_y_formation', 135),
                'size' => (int) Configuration::get('certificat_font_size_formation', 20),
            ],
            'performance' => [
                'x' => (int) Configuration::get('certificat_axis_x_performance', 148),
                'y' => (int) Configuration::get('certificat_axis_y_performance', 155),
                'size' => (int) Configuration::get('certificat_font_size_perf', 12),
            ],
            'metadata' => [
                'x' => (int) Configuration::get('certificat_axis_x_metadata', 40),
                'y' => (int) Configuration::get('certificat_axis_y_metadata', 185),
            ],
        ];

        $fontColor = Configuration::get('certificat_font_color_name', '#FFFFFF');
        $showNote = (bool) Configuration::get('certificat_show_note', 1);
        $showMention = (bool) Configuration::get('certificat_show_mention', 1);
        $showQrCode = (bool) Configuration::get('certificat_show_qrcode', 1);
        $qrSize = (int) Configuration::get('certificat_qr_size', 100);

        // ===== GÉNÉRER LE QR CODE EN DATA URI POUR LE PDF =====
        $qrCodeDataUri = null;
        if ($showQrCode) {
            $qrCodeDataUri = $this->genererQrCodeDataUri($certificat);
        }

        // ===== DONNÉES POUR LA VUE =====
        $data = [
            'certificat' => $certificat,
            'backgroundImage' => $backgroundImage,
            'positions' => $positions,
            'fontColor' => $fontColor,
            'showNote' => $showNote,
            'showMention' => $showMention,
            'showQrCode' => $showQrCode,
            'qrSize' => $qrSize,
            'qrCodeDataUri' => $qrCodeDataUri,
        ];

        // ===== PDF =====
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('client.pdf.certificat', $data)
                ->setPaper('a4', 'landscape');
            return $pdf->download($nomFichier . '.pdf');
        }

        // ===== JPG / PNG =====
        try {
            $imagePath = $this->genererFichierCertificat($certificat);
            $fullPath = storage_path('app/public/' . $imagePath);

            if (!file_exists($fullPath)) {
                abort(404, 'Fichier image introuvable.');
            }

            $contentType = $format === 'png' ? 'image/png' : 'image/jpeg';
            $extension = $format === 'png' ? 'png' : 'jpg';

            return response()->file($fullPath, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $nomFichier . '.' . $extension . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur génération image certificat: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la génération du certificat.');
        }
    }

    /**
     * Créer un duplicata (logique centralisée)
     */
    // Dans CertificatController, méthode creerDuplicata()
    private function creerDuplicata(Certificat $original): Certificat
    {
        return Certificat::create([
            'user_id'           => $original->user_id,
            'formation_id'      => $original->formation_id,
            'session_qcm_id'    => $original->session_qcm_id,
            'numero_certificat' => Certificat::genererNumero() . '-DUP',
            'note_obtenue'      => $original->note_obtenue,
            'delivre_le'        => null,
            'telecharge'        => true,
            'parent_id'         => $original->id,
            'verification_token' => \Illuminate\Support\Str::uuid(), // ✅ Token unique
        ]);
    }

    /**
     * Écrire un texte sur l'image
     */
    private function ecrireTexte($image, string $texte, array $position, $fontPath, $couleur)
    {
        if (is_string($fontPath)) {
            imagettftext(
                $image,
                $position['size'],
                0,
                $position['x'],
                $position['y'],
                $couleur,
                $fontPath,
                $texte
            );
        } else {
            imagestring(
                $image,
                $fontPath,
                $position['x'],
                $position['y'],
                $texte,
                $couleur
            );
        }
    }

    /**
     * Ajouter le QR Code sur l'image
     */
    private function ajouterQrCode($image, Certificat $certificat)
    {
        try {
            $urlVerification = route('certificat.verification', ['token' => $certificat->verification_token]);
            $qrSize = (int) Configuration::get('certificat_qr_size', 100);

            $qrCodePngRaw = QrCode::format('png')
                ->size($qrSize)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($urlVerification);

            $qrImage = imagecreatefromstring($qrCodePngRaw);

            if ($qrImage) {
                $qrX = (int) Configuration::get('certificat_axis_x_metadata', 40);
                $qrY = (int) Configuration::get('certificat_axis_y_metadata', 185);

                imagecopy($image, $qrImage, $qrX, $qrY, 0, 0, $qrSize, $qrSize);
                imagedestroy($qrImage);
            }
        } catch (\Exception $e) {
            Log::warning('Erreur génération QR Code: ' . $e->getMessage());
        }
    }

        /**
         * Calculer la mention en fonction de la note
         */
        private function calculerMention(float $note): string
        {
            if ($note >= 18) return 'Très Bien';
            if ($note >= 16) return 'Bien';
            if ($note >= 14) return 'Assez Bien';
            if ($note >= 12) return 'Passable';
            return 'Insuffisant';
        }

        /**
     * ============================================================
     * GÉNÉRER UN CERTIFICAT SPÉCIMEN (ADMIN)
     * ============================================================
     */
    public function specimen()
    {
        // Créer un certificat factice pour le spécimen
        $certificat = new Certificat();
        $certificat->numero_certificat = 'SPECIMEN-' . strtoupper(\Illuminate\Support\Str::random(8));
        $certificat->verification_token = 'specimen-' . \Illuminate\Support\Str::uuid();
        $certificat->note_obtenue = 18.5;
        
        // Créer un utilisateur factice
        $certificat->user = (object) [
            'name' => 'KOUASSI Yao Jean-Marc',
        ];
        
        // Créer une formation factice
        $certificat->formation = (object) [
            'titre' => 'Développement Web Full Stack',
        ];
        
        // Créer une session QCM factice avec niveau
        $certificat->session = (object) [
            'qcm' => (object) [
                'niveau' => (object) [
                    'nom' => 'Niveau Expert',
                ],
            ],
        ];
        
        // Mention
        $certificat->mention = $this->calculerMention($certificat->note_obtenue);
        
        // Données pour la vue
        $backgroundPath = Configuration::get('certificat_background', 'certificats/default_bg.jpg');
        $backgroundImage = asset('storage/' . $backgroundPath);

        $positions = [
            'numero' => [
                'x' => (int) Configuration::get('certificat_axis_x_numero', 240),
                'y' => (int) Configuration::get('certificat_axis_y_numero', 20),
                'size' => (int) Configuration::get('certificat_font_size_numero', 12),
            ],
            'name' => [
                'x' => (int) Configuration::get('certificat_axis_x_name', 148),
                'y' => (int) Configuration::get('certificat_axis_y_name', 105),
                'size' => (int) Configuration::get('certificat_font_size_name', 28),
            ],
            'formation' => [
                'x' => (int) Configuration::get('certificat_axis_x_formation', 148),
                'y' => (int) Configuration::get('certificat_axis_y_formation', 135),
                'size' => (int) Configuration::get('certificat_font_size_formation', 20),
            ],
            'performance' => [
                'x' => (int) Configuration::get('certificat_axis_x_performance', 148),
                'y' => (int) Configuration::get('certificat_axis_y_performance', 155),
                'size' => (int) Configuration::get('certificat_font_size_perf', 12),
            ],
            'metadata' => [
                'x' => (int) Configuration::get('certificat_axis_x_metadata', 40),
                'y' => (int) Configuration::get('certificat_axis_y_metadata', 185),
            ],
        ];

        $fontColor = Configuration::get('certificat_font_color_name', '#FFFFFF');
        $showNote = (bool) Configuration::get('certificat_show_note', 1);
        $showMention = (bool) Configuration::get('certificat_show_mention', 1);
        $showQrCode = (bool) Configuration::get('certificat_show_qrcode', 1);
        $qrSize = (int) Configuration::get('certificat_qr_size', 100);

        // QR Code spécimen
        $qrCodeDataUri = null;
        if ($showQrCode) {
            $qrCodeDataUri = $this->genererQrCodeDataUri($certificat);
        }

        $data = [
            'certificat' => $certificat,
            'backgroundImage' => $backgroundImage,
            'positions' => $positions,
            'fontColor' => $fontColor,
            'showNote' => $showNote,
            'showMention' => $showMention,
            'showQrCode' => $showQrCode,
            'qrSize' => $qrSize,
            'qrCodeDataUri' => $qrCodeDataUri,
        ];

        // Générer le PDF
        $pdf = Pdf::loadView('client.pdf.certificat', $data)
            ->setPaper('a4', 'landscape');
        
        // Ajouter un filigrane "SPÉCIMEN"
        $pdf->getDomPDF()->getCanvas()->page_text(
            400, 300, 
            'SPÉCIMEN', 
            null, 80, 
            array(0, 0, 0, 0.05)
        );

        return $pdf->download('specimen-certificat.pdf');
    }
}