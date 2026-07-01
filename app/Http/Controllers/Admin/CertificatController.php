<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
     * 1. LISTE ADMIN - Tous les certificats
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
     * 2. TÉLÉCHARGER (ADMIN)
     * ============================================================
     */
    public function telecharger(Request $request, Certificat $certificat)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        $format = $request->input('format', 'pdf');
        $formatsAutorises = ['pdf', 'jpg', 'jpeg', 'png'];

        if (!in_array($format, $formatsAutorises)) {
            $format = 'pdf';
        }

        $certificat->load(['user', 'formation', 'session.qcm.niveau']);
        return $this->genererSortie($certificat, $format);
    }

    /**
     * ============================================================
     * 3. APERÇU
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

        $backgroundImage = asset('storage/' . $this->getBackgroundPath());

        extract($this->getRenderingConfig());

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
     * 4. CRÉER UN DUPLICATA (ADMIN - Direct, sans paiement)
     * ============================================================
     * ⚠️ Override administratif explicite (perte, cas exceptionnel).
     * Ne suit pas le flux de paiement standard du client. À réserver
     * aux admins de confiance (déjà protégé par hasRole('admin')).
     */
    public function duplicata(Certificat $certificat)
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        // Vérifier qu'il n'y a pas une demande déjà en cours ou traitée
        $demandeExistante = DemandeDuplicata::where('certificat_id', $certificat->id)
            ->whereIn('statut', ['en_attente', 'paye', 'valide'])
            ->exists();

        if ($demandeExistante) {
            return back()->with('error', 'Une demande de duplicata est déjà en cours pour ce certificat.');
        }

        $duplicata = $this->creerDuplicata($certificat);
        $duplicata->update([
            'delivre_le' => now(),
            'telecharge' => false,
        ]);

        // ✅ Notifier le client (manquait dans la version précédente)
        Notification::create([
            'user_id' => $certificat->user_id,
            'titre'   => '✅ Duplicata disponible !',
            'message' => 'Un duplicata pour la formation "' . ($certificat->formation->titre ?? '') . '" a été généré par l\'administration et est disponible. Téléchargez-le maintenant.',
            'type'    => 'success',
            'lien'    => route('client.certificats.telecharger', ['certificat' => $duplicata->id, 'format' => 'pdf']),
        ]);

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

        // ✅ Le paiement est OBLIGATOIRE avant toute validation.
        // Le bouton "Valider" ne doit même pas être visible côté vue
        // tant que statut !== 'paye' (cf. demandes.blade.php).
        if ($demande->statut !== 'paye') {
            return back()->with('error', 'Cette demande ne peut pas être validée : le paiement n\'a pas encore été effectué.');
        }

        $certificatOriginal = $demande->certificat;
        $duplicata = $this->creerDuplicata($certificatOriginal);

        $demande->update([
            'statut' => 'valide',
            'valide_le' => now(),
        ]);

        $duplicata->update([
            'delivre_le' => now(),
            'telecharge' => false,
        ]);

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

        // Le rejet reste possible avant ET après paiement
        // (ex: certificat invalide détecté avant même le paiement).
        if (!in_array($demande->statut, ['en_attente', 'paye'])) {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

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

        $demandes = DemandeDuplicata::with(['certificat.formation', 'user', 'paiement'])
            ->whereIn('statut', ['en_attente', 'paye', 'valide'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => DemandeDuplicata::count(),
            'en_attente' => DemandeDuplicata::where('statut', 'en_attente')->count(),
            'paye' => DemandeDuplicata::where('statut', 'paye')->count(),
            'valide' => DemandeDuplicata::where('statut', 'valide')->count(),
            'rejete' => DemandeDuplicata::where('statut', 'rejete')->count(),
        ];

        return view('admin.duplicatas.demandes', compact('demandes', 'stats'));
    }

    /**
     * ============================================================
     * 8. VÉRIFICATION PUBLIQUE (Jeton)
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
     * 9. GÉNÉRER UN CERTIFICAT SPÉCIMEN (ADMIN)
     * ============================================================
     */
    public function specimen()
    {
        $certificat = new Certificat();
        $certificat->id = 0;
        $certificat->numero_certificat = 'SPECIMEN-' . strtoupper(\Illuminate\Support\Str::random(8));
        $certificat->verification_token = 'specimen-' . \Illuminate\Support\Str::uuid();
        $certificat->note_obtenue = 18.5;
        $certificat->delivre_le = now();
        $certificat->created_at = now();
        $certificat->telecharge = false;

        $certificat->user = (object) [
            'prenom' => 'Yao Jean-Marc',
            'nom'    => 'KOUASSI',
            'name'   => 'KOUASSI Yao Jean-Marc',
            'email'  => 'specimen@excellencedigital.ci',
        ];

        $certificat->formation = (object) [
            'titre' => 'Développement Web Full Stack',
        ];

        $certificat->session = (object) [
            'qcm' => (object) [
                'niveau' => (object) ['nom' => 'Niveau Expert'],
            ],
        ];

        $certificat->mention = $this->calculerMention($certificat->note_obtenue);

        $bgFullPath = storage_path('app/public/' . $this->getBackgroundPath());

        if (file_exists($bgFullPath)) {
            $mime = mime_content_type($bgFullPath);
            $backgroundImage = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($bgFullPath));
        } else {
            $defaultBgPath = storage_path('app/public/certificats/default_bg.jpg');
            $backgroundImage = file_exists($defaultBgPath)
                ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($defaultBgPath))
                : null;
        }

        extract($this->getRenderingConfig());

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

        $pdf = Pdf::loadView('client.pdf.certificat', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('specimen-certificat.pdf');
    }

    /**
     * ============================================================
     * 10. MÉTHODES PRIVÉES (Helpers)
     * ============================================================
     */

    /**
     * Chemin de fond configuré (relatif à storage/app/public)
     */
    private function getBackgroundPath(): string
    {
        return Configuration::get('certificat_background', 'certificats/default_bg.jpg');
    }

    /**
     * Config centralisée des positions/styles du certificat.
     * Évite de dupliquer ce bloc dans apercu(), specimen(), genererSortie()
     * et genererFichierCertificat() — une seule source de vérité.
     */
    private function getRenderingConfig(): array
    {
        return [
            'positions' => [
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
            ],
            'fontColor' => Configuration::get('certificat_font_color_name', '#FFFFFF'),
            'showNote' => (bool) Configuration::get('certificat_show_note', 1),
            'showMention' => (bool) Configuration::get('certificat_show_mention', 1),
            'showQrCode' => (bool) Configuration::get('certificat_show_qrcode', 1),
            'qrSize' => (int) Configuration::get('certificat_qr_size', 100),
        ];
    }

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

    private function genererSortie(Certificat $certificat, string $format)
    {
        $nomFichier = 'certificat-' . $certificat->numero_certificat;
        $backgroundImage = asset('storage/' . $this->getBackgroundPath());

        extract($this->getRenderingConfig());

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

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('client.pdf.certificat', $data)
                ->setPaper('a4', 'landscape');
            return $pdf->download($nomFichier . '.pdf');
        }

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

    private function creerDuplicata(Certificat $original): Certificat
    {
        return Certificat::create([
            'user_id'           => $original->user_id,
            'formation_id'      => $original->formation_id,
            'session_qcm_id'    => $original->session_qcm_id,
            'numero_certificat' => Certificat::genererNumero() . '-DUP',
            'note_obtenue'      => $original->note_obtenue,
            'delivre_le'        => null,
            'telecharge'        => false,
            'parent_id'         => $original->id,
            'verification_token' => \Illuminate\Support\Str::uuid(),
        ]);
    }

    private function genererFichierCertificat(Certificat $certificat)
    {
        $bgRelativePath = $this->getBackgroundPath();
        $bgRelativePath = str_replace(['..', '\\'], '', $bgRelativePath);
        $bgPath = storage_path('app/public/' . $bgRelativePath);

        if (!file_exists($bgPath)) {
            $defaultBg = storage_path('app/public/certificats/default_bg.jpg');
            $bgPath = file_exists($defaultBg) ? $defaultBg : null;
        }

        if (!$bgPath || !file_exists($bgPath)) {
            abort(404, "Image de fond de la maquette introuvable.");
        }

        // ✅ Détection dynamique du format au lieu d'un imagecreatefromjpeg()
        // figé qui plantait silencieusement si l'admin uploadait un PNG/WebP.
        $mimeType = mime_content_type($bgPath);
        $image = match ($mimeType) {
            'image/png'  => imagecreatefrompng($bgPath),
            'image/webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($bgPath) : null,
            'image/jpeg' => imagecreatefromjpeg($bgPath),
            default      => null,
        };

        if (!$image) {
            abort(500, "Format d'image de fond non supporté (JPEG, PNG ou WebP uniquement).");
        }

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
            $fontPath = 5;
        }

        $hexColor = Configuration::get('certificat_font_color_name', '#FFFFFF');
        list($r, $g, $b) = sscanf($hexColor, '#%02x%02x%02x');
        $couleurTexte = imagecolorallocate($image, $r, $g, $b);

        $config = $this->getRenderingConfig();
        $positions = $config['positions'];

        $this->ecrireTexte($image, $certificat->numero_certificat, $positions['numero'], $fontPath, $couleurTexte);
        $this->ecrireTexte($image, strtoupper($certificat->user->name ?? 'Utilisateur'), $positions['name'], $fontPath, $couleurTexte);
        $this->ecrireTexte($image, $certificat->formation->titre ?? 'Formation', $positions['formation'], $fontPath, $couleurTexte);

        if (Configuration::get('certificat_show_note', 1)) {
            $note = $certificat->note_obtenue ?? 0;
            $mention = $this->calculerMention($note);
            $performanceTxt = "Note : " . number_format($note, 1) . "/20 | Mention : " . $mention;
            $this->ecrireTexte($image, $performanceTxt, $positions['performance'], $fontPath, $couleurTexte);
        }

        if (Configuration::get('certificat_show_qrcode', 1)) {
            $this->ajouterQrCode($image, $certificat);
        }

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

    private function calculerMention(float $note): string
    {
        if ($note >= 18) return 'Très Bien';
        if ($note >= 16) return 'Bien';
        if ($note >= 14) return 'Assez Bien';
        if ($note >= 12) return 'Passable';
        return 'Insuffisant';
    }
}