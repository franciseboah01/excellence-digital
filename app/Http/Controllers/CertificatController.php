<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificatController extends Controller
{
    // ===== TÉLÉCHARGER =====
    public function telecharger(Certificat $certificat)
    {
        // L'administrateur peut télécharger sans limite
        if (auth()->user()->hasRole('admin')) {
            $certificat->load(['user', 'formation', 'session.qcm.niveau']);

            $pdf = Pdf::loadView('client.pdf.certificat', compact('certificat'))
                ->setPaper('a4', 'landscape');

            return $pdf->download(
                'certificat-' . $certificat->numero_certificat . '.pdf'
            );
        }

        // Vérifier que le certificat appartient au client connecté
        abort_if($certificat->user_id !== auth()->id(), 403);

        // Sécurité anti double clic / multi-onglets :
        // on modifie uniquement si telecharge est encore à false
        $miseAJour = Certificat::where('id', $certificat->id)
            ->where('telecharge', false)
            ->update([
                'telecharge' => true
            ]);

        // Si aucune ligne n'a été modifiée, le certificat a déjà été utilisé
        if ($miseAJour === 0) {
            return back()->with(
                'error',
                'Vous avez déjà téléchargé ce certificat. Contactez l’administration pour obtenir un duplicata.'
            );
        }

        // Recharger les données complètes pour le PDF
        $certificat->refresh();
        $certificat->load(['user', 'formation', 'session.qcm.niveau']);

        $pdf = Pdf::loadView('client.pdf.certificat', compact('certificat'))
            ->setPaper('a4', 'landscape');

        return $pdf->download(
            'certificat-' . $certificat->numero_certificat . '.pdf'
        );
    }


    // ===== APERÇU =====
    public function apercu(Certificat $certificat)
    {
        abort_if(
            $certificat->user_id !== auth()->id()
            && !auth()->user()->hasRole('admin'),
            403
        );

        $certificat->load([
            'user',
            'formation',
            'session.qcm.niveau'
        ]);

        return view('client.pdf.certificat', compact('certificat'));
    }


    // ===== LISTE ADMIN =====
    public function index()
    {
        $certificats = Certificat::with([
                'user',
                'formation'
            ])
            ->latest()
            ->paginate(10);

        $stats = [
            'total'   => Certificat::count(),
            'ce_mois' => Certificat::whereMonth(
                'created_at',
                now()->month
            )->count(),
            'moyenne' => round(
                Certificat::avg('note_obtenue'),
                2
            ),
        ];

        return view(
            'admin.certificats.index',
            compact('certificats', 'stats')
        );
    }


    // ===== DUPLICATA (ADMIN) =====
    public function duplicata(Certificat $certificat)
    {
        abort_unless(
            auth()->user()->hasRole('admin'),
            403
        );

        $duplicata = Certificat::create([
            'user_id'           => $certificat->user_id,
            'formation_id'      => $certificat->formation_id,
            'session_qcm_id'    => $certificat->session_qcm_id,
            'numero_certificat' => Certificat::genererNumero() . '-DUP',
            'note_obtenue'      => $certificat->note_obtenue,
            'delivre_le'        => now(),
            'telecharge'        => false,
        ]);

        return back()->with(
            'success',
            'Duplicata créé : ' . $duplicata->numero_certificat
        );
    }
    public function demandeDuplicata(Certificat $certificat)
    {
        abort_if($certificat->user_id !== auth()->id(), 403);

        session(['certificat_id' => $certificat->id]);
        
        // Rediriger vers le paiement (service fictif "Duplicata certificat")
        return redirect()->route('client.paiement.form', ['service', 'duplicata'])
            ->with('certificat_id', $certificat->id);
    }
}