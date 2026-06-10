<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Duplicata extends Model
{
    public function duplicata(Certificat $certificat)
    {
        // Générer un nouveau numéro pour le duplicata
        $duplicata = Certificat::create([
            'user_id'           => $certificat->user_id,
            'formation_id'      => $certificat->formation_id,
            'session_qcm_id'    => $certificat->session_qcm_id,
            'numero_certificat' => Certificat::genererNumero() . '-DUP',
            'note_obtenue'      => $certificat->note_obtenue,
            'delivre_le'        => now(),
            'telecharge'        => false,
        ]);

        return redirect()->route('admin.certificats.index')
            ->with('success', 'Duplicata créé avec le numéro ' . $duplicata->numero_certificat);
    }
}
