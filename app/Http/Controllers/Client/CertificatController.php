<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Certificat;
use App\Models\Configuration;
use App\Models\DemandeDuplicata;
use App\Models\Notification;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificatController extends Controller
{
    // ===== LISTE CERTIFICATS =====
    public function index()
    {
        $user = auth()->user();

        $certificats = Certificat::where('user_id', $user->id)
            ->with(['formation', 'demandesDuplicata' => function ($query) {
                $query->whereIn('statut', ['en_attente', 'paye', 'valide']);
            }])
            ->latest('delivre_le')
            ->get();

        // Propriétés calculées NON couvertes par les accessors du modèle Certificat.
        // (est_duplicata, est_telechargeable, mention sont déjà fournis automatiquement
        // par le modèle via getEstDuplicataAttribute / getEstTelechargeableAttribute / getMentionAttribute)
        foreach ($certificats as $certificat) {
            $certificat->prix_duplicata = Configuration::get('duplicata_prix', 1000);
            $certificat->demande_existante = $certificat->demandesDuplicata->isNotEmpty();
            $certificat->duplicata_existant = Certificat::where('parent_id', $certificat->id)
                ->where('telecharge', false)
                ->exists();
        }

        return view('client.certificats.index', compact('certificats'));
    }

    // ===== TÉLÉCHARGER CERTIFICAT =====
    public function telecharger(Certificat $certificat, $format = 'pdf')
    {
        abort_if($certificat->user_id !== auth()->id(), 403);
        abort_if(!in_array($format, ['pdf', 'jpg']), 404);

        // Vérifier si téléchargeable (accessor du modèle)
        if (!$certificat->est_telechargeable) {
            if ($certificat->est_duplicata) {
                return back()->with('error', 'Ce duplicata n\'est pas encore disponible.');
            }
            if ($certificat->telecharge) {
                return back()->with('error', 'Vous avez déjà téléchargé ce certificat original. Vous pouvez demander un duplicata.');
            }
        }

        // Marquer comme téléchargé
        if (!$certificat->telecharge) {
            $certificat->update(['telecharge' => true]);
        }

        // Charger les relations
        $certificat->load(['user', 'formation', 'session.qcm.niveau']);

        // Générer le PDF
        return $this->genererPDF($certificat);
    }

    // ===== DEMANDE DUPLICATA =====
    public function demandeDuplicata(Certificat $certificat)
    {
        abort_if($certificat->user_id !== auth()->id(), 403);
        abort_if($certificat->est_duplicata, 403, 'Impossible de faire un duplicata d\'un duplicata.');

        // ✅ Si une demande 'en_attente' existe déjà pour ce certificat (le client
        // a cliqué une première fois mais n'a jamais terminé le paiement — session
        // perdue, navigateur fermé...), on relance simplement le paiement au lieu
        // de le bloquer définitivement avec une erreur.
        $demandeEnAttente = DemandeDuplicata::where('certificat_id', $certificat->id)
            ->where('user_id', auth()->id())
            ->where('statut', 'en_attente')
            ->latest()
            ->first();

        if ($demandeEnAttente) {
            session(['certificat_id' => $certificat->id]);
            return redirect()->route('client.paiement.form', ['type' => 'service', 'id' => 'duplicata']);
        }

        if (!DemandeDuplicata::peutDemander($certificat)) {
            return back()->with('error', 'Une demande de duplicata est déjà payée (en attente de validation), ou un duplicata est déjà disponible pour ce certificat.');
        }

        // Créer la demande (en attente de paiement)
        $demande = DemandeDuplicata::create([
            'certificat_id' => $certificat->id,
            'user_id' => auth()->id(),
            'statut' => 'en_attente',
            'paye' => false,
            'montant_paye' => Configuration::get('duplicata_prix', 1000),
        ]);

        // Notifier TOUS les administrateurs (au lieu d'un user_id=1 codé en dur)
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'titre' => '📄 Demande de duplicata',
                'message' => auth()->user()->prenom . ' ' . auth()->user()->nom . ' a demandé un duplicata pour ' . $certificat->formation->titre,
                'type' => 'info',
                'lien' => route('admin.duplicatas.demandes'),
            ]);
        }

        // ✅ Rediriger vers la page de paiement avec l'ID du certificat directement
        // dans l'URL (type='duplicata', id=certificat réel). On évite volontairement
        // de faire transiter cette information par la session : une session perdue
        // (expiration, changement d'onglet, redémarrage serveur) rendait auparavant
        // le paiement impossible à finaliser sans revenir depuis "Mes Certificats".
        return redirect()
            ->route('client.paiement.form', ['type' => 'duplicata', 'id' => $certificat->id])
            ->with('info', '✅ Demande créée. Merci de procéder au paiement de ' . number_format($demande->montant_paye, 0, ',', ' ') . ' FCFA pour finaliser votre duplicata.');
    }

    // ===== MÉTHODES PRIVÉES =====

    private function genererPDF(Certificat $certificat)
    {
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

        return $pdf->download('certificat-' . $certificat->numero_certificat . '.pdf');
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
            Log::error('Erreur génération QR Code: ' . $e->getMessage());
            return null;
        }
    }
}