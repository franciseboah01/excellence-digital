<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'user_id', 'formation_id', 'service_id', 'demande_id', 
        'certificat_id', // <-- CORRIGÉ : Ajouté ici pour autoriser l'enregistrement
        'montant_total', 'montant_paye', 'statut',
        'mode_paiement', 'reference', 'notes',
        'enregistre_par', 'date_paiement',
    ];

    protected $casts = [
        'date_paiement' => 'datetime',
        'montant_total' => 'decimal:2',
        'montant_paye'  => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function certificat()
    {
        return $this->belongsTo(Certificat::class, 'certificat_id');
    }

    public function demande()
    {
        return $this->belongsTo(DemandeService::class, 'demande_id');
    }

    public function enregistrePar()
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    // Montant restant
    public function getMontantRestantAttribute(): float
    {
        return max(0, $this->montant_total - $this->montant_paye);
    }

    // Pourcentage payé
    public function getPourcentageAttribute(): int
    {
        if ($this->montant_total == 0) return 0;
        return min(100, (int)(($this->montant_paye / $this->montant_total) * 100));
    }

    // Générer une référence unique
    public static function genererReference(): string
    {
        return 'EDC-' . strtoupper(uniqid());
    }
}