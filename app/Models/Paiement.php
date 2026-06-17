<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'user_id',
        'formation_id',
        'service_id',
        'demande_id',
        'certificat_id',
        'montant_total',
        'montant_paye',
        'statut',
        'mode_paiement',
        'reference',
        'notes',
        'enregistre_par', // ← CONFIRMÉ
        'date_paiement',
    ];

    protected $casts = [
        'date_paiement' => 'datetime',
        'montant_total' => 'decimal:2',
        'montant_paye'  => 'decimal:2',
    ];

    // ===== RELATIONS =====

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

    /**
     * Utilisateur qui a enregistré le paiement
     */
    public function enregistrePar()
    {
        return $this->belongsTo(User::class, 'enregistre_par');
    }

    /**
     * Demande de duplicata associée
     */
    public function demandeDuplicata()
    {
        return $this->hasOne(DemandeDuplicata::class);
    }

    // ===== ACCESSORS =====

    /**
     * Montant restant à payer
     */
    public function getMontantRestantAttribute(): float
    {
        return max(0, $this->montant_total - $this->montant_paye);
    }

    /**
     * Pourcentage payé
     */
    public function getPourcentageAttribute(): int
    {
        if ($this->montant_total == 0) {
            return 0;
        }
        return min(100, (int) (($this->montant_paye / $this->montant_total) * 100));
    }

    /**
     * Vérifier si le paiement est complet
     */
    public function getEstCompletAttribute(): bool
    {
        return $this->statut === 'complete' && $this->montant_restant <= 0;
    }

    // ===== SCOPES =====

    /**
     * Scope : Paiements complets uniquement
     */
    public function scopeComplets($query)
    {
        return $query->where('statut', 'complete');
    }

    /**
     * Scope : Paiements en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    /**
     * Scope : Paiements échoués
     */
    public function scopeEchoues($query)
    {
        return $query->where('statut', 'echoue');
    }

    /**
     * Scope : Paiements par mode
     */
    public function scopeParMode($query, $mode)
    {
        return $query->where('mode_paiement', $mode);
    }

    /**
     * Scope : Paiements d'un type spécifique
     */
    public function scopeDeType($query, $type)
    {
        if ($type === 'formation') {
            return $query->whereNotNull('formation_id');
        }
        if ($type === 'service') {
            return $query->whereNotNull('service_id');
        }
        if ($type === 'certificat') {
            return $query->whereNotNull('certificat_id');
        }
        return $query;
    }

    // ===== MÉTHODES STATIQUES =====

    /**
     * Générer une référence unique
     */
    public static function genererReference(): string
    {
        return 'EDC-' . strtoupper(uniqid()) . '-' . date('Y');
    }

    // ===== MÉTHODES =====

    /**
     * Valider le paiement
     */
    public function valider(): bool
    {
        if ($this->statut === 'complete') {
            return false;
        }

        $this->update([
            'statut' => 'complete',
            'montant_paye' => $this->montant_total,
            'date_paiement' => now(),
        ]);

        return true;
    }

    /**
     * Annuler le paiement
     */
    public function annuler(): bool
    {
        if ($this->statut === 'annule') {
            return false;
        }

        $this->update(['statut' => 'annule']);
        return true;
    }
}