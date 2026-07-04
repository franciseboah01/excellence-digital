<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificat;
use Illuminate\Http\Request;

class AdminCertificatController extends Controller
{
    public function index(Request $request)
    {
        $certificats = Certificat::with(['user:id,nom,prenom,email', 'formation:id,titre'])
            ->when($request->formation_id, fn($q) => $q->where('formation_id', $request->formation_id))
            ->when($request->search, fn($q) => $q->whereHas('user', fn($q) => 
                $q->where('nom', 'LIKE', "%{$request->search}%")
                  ->orWhere('prenom', 'LIKE', "%{$request->search}%")
            ))
            ->latest()
            ->paginate(20);

        return response()->json($certificats);
    }

    public function duplicata(Certificat $certificat)
    {
        // Logique de génération de duplicata
        $duplicata = $certificat->replicate();
        $duplicata->est_duplicata = true;
        $duplicata->date_duplicata = now();
        $duplicata->save();

        return response()->json([
            'message' => 'Duplicata généré.',
            'certificat' => $duplicata
        ]);
    }

    public function telecharger(Certificat $certificat)
    {
        // Logique de génération PDF
        return response()->json([
            'message' => 'Téléchargement du certificat.',
            'certificat_id' => $certificat->id,
            // 'url' => URL signée pour téléchargement
        ]);
    }

    public function demandesDuplicata()
    {
        $demandes = \App\Models\DemandeDuplicata::with(['user:id,nom,prenom', 'certificat'])
            ->where('statut', 'en_attente')
            ->latest()
            ->get();

        return response()->json($demandes);
    }

    public function validerDuplicata(\App\Models\DemandeDuplicata $demande)
    {
        $demande->update(['statut' => 'valide', 'date_validation' => now()]);

        // Générer le duplicata
        $this->duplicata($demande->certificat);

        return response()->json(['message' => 'Demande de duplicata validée.']);
    }

    public function rejeterDuplicata(\App\Models\DemandeDuplicata $demande, Request $request)
    {
        $request->validate(['motif' => 'nullable|string']);
        
        $demande->update([
            'statut' => 'rejete',
            'motif_rejet' => $request->motif
        ]);

        return response()->json(['message' => 'Demande de duplicata rejetée.']);
    }

    public function specimen()
    {
        // Retourne le spécimen de certificat configuré
        return response()->json([
            'modele' => \App\Models\Configuration::where('cle', 'certificat_modele')->first()?->valeur,
            'signature' => \App\Models\Configuration::where('cle', 'certificat_signature')->first()?->valeur,
        ]);
    }
}