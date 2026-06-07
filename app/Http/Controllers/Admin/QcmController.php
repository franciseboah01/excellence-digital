<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Qcm;
use App\Models\SessionQcm;
use App\Models\Formation;
use App\Models\Certificat;
use Illuminate\Http\Request;

class QcmController extends Controller
{
    // ===== LISTE TOUS LES QCMs =====
    public function index()
    {
        $qcms = Qcm::with(['formation', 'niveau', 'createur'])
            ->withCount(['questions', 'sessions'])
            ->latest()->paginate(15);

        $stats = [
            'total'        => Qcm::count(),
            'actifs'       => Qcm::where('actif', true)->count(),
            'sessions'     => SessionQcm::count(),
            'certificats'  => Certificat::count(),
            'taux_reussite'=> SessionQcm::count() > 0
                ? round(SessionQcm::where('reussi', true)->count() / SessionQcm::count() * 100)
                : 0,
        ];

        return view('admin.qcms.index', compact('qcms', 'stats'));
    }

    // ===== DÉTAIL D'UN QCM =====
    public function show(Qcm $qcm)
    {
        $qcm->load(['formation', 'niveau', 'createur', 'questions.reponses']);

        $sessions = SessionQcm::where('qcm_id', $qcm->id)
            ->with('user')
            ->latest()
            ->paginate(10);

        $stats = [
            'total'        => SessionQcm::where('qcm_id', $qcm->id)->count(),
            'reussis'      => SessionQcm::where('qcm_id', $qcm->id)->where('reussi', true)->count(),
            'moyenne'      => round(SessionQcm::where('qcm_id', $qcm->id)->avg('note'), 2),
            'meilleure'    => SessionQcm::where('qcm_id', $qcm->id)->max('note'),
            'certificats'  => Certificat::where('formation_id', $qcm->formation_id)->count(),
        ];

        return view('admin.qcms.show', compact('qcm', 'sessions', 'stats'));
    }

    // ===== ACTIVER / DÉSACTIVER =====
    public function toggleActif(Qcm $qcm)
    {
        $qcm->update(['actif' => !$qcm->actif]);

        return back()->with('success',
            $qcm->actif ? '✅ QCM activé !' : '⏸️ QCM désactivé.'
        );
    }

    // ===== SUPPRIMER =====
    public function destroy(Qcm $qcm)
    {
        $qcm->delete();
        return back()->with('success', 'QCM supprimé.');
    }

    // ===== LISTE DES CERTIFICATS =====
    public function certificats()
    {
        $certificats = Certificat::with(['user', 'formation', 'session'])
            ->latest()->paginate(20);

        $stats = [
            'total'    => Certificat::count(),
            'ce_mois'  => Certificat::whereMonth('delivre_le', now()->month)->count(),
            'moyenne'  => round(Certificat::avg('note_obtenue'), 2),
        ];

        return view('admin.qcms.certificats', compact('certificats', 'stats'));
    }
}