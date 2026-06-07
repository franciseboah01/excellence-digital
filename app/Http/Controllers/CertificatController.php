<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class CertificatController extends Controller
{
    /**
     * Télécharger le certificat en PDF paysage
     */
    public function telecharger(Certificat $certificat)
    {
        abort_if($certificat->user_id !== auth()->id(), 403);

        $certificat->load(['user', 'formation', 'session.qcm.niveau']);

        $pdf = Pdf::loadView('pdf.certificat', compact('certificat'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'dpi'                => 150,
                'defaultFont'        => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'    => false,
            ]);

        $nomFichier = 'certificat-' . str_replace(' ', '-', strtolower($certificat->formation->titre))
            . '-' . $certificat->numero_certificat . '.pdf';

        return $pdf->download($nomFichier);
    }

    /**
     * Aperçu du certificat en ligne
     */
    public function apercu(Certificat $certificat)
    {
        abort_if($certificat->user_id !== auth()->id(), 403);
        $certificat->load(['user', 'formation', 'session.qcm.niveau']);
        return view('pdf.certificat', compact('certificat'));
    }
}