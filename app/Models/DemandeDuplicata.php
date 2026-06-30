<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeDuplicata extends Model
{
    protected $fillable = [
        'certificat_id',
        'user_id',
        'paiement_id',
        'statut',        // en_attente, paye, valide, rejete
        'paye',
        'montant_paye',
        'motif_rejet',
        'valide_le',
    ];

    protected $casts = [
        'paye' => 'boolean',
        'montant_paye' => 'integer',
        'valide_le' => 'datetime',
    ];

    // ===== RELATIONS =====

    /**
     * Certificat original concerné
     */
    public function certificat()
    {
        return $this->belongsTo(Certificat::class);
    }

    /**
     * Utilisateur ayant fait la demande
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Paiement associé
     */
    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    // ===== ACCESSORS =====

    /**
     * Vérifier si la demande est en attente
     */
    public function getEstEnAttenteAttribute(): bool
    {
        return $this->statut === 'en_attente';
    }

    /**
     * Vérifier si la demande est payée
     */
    public function getEstPayeAttribute(): bool
    {
        return $this->statut === 'paye';
    }

    /**
     * Vérifier si la demande est validée
     */
    public function getEstValideAttribute(): bool
    {
        return $this->statut === 'valide';
    }

    /**
     * Vérifier si la demande est rejetée
     */
    public function getEstRejeteAttribute(): bool
    {
        return $this->statut === 'rejete';
    }

    // ===== SCOPES =====

    /**
     * Scope : Demandes en attente
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    /**
     * Scope : Demandes payées
     */
    public function scopePayees($query)
    {
        return $query->where('statut', 'paye');
    }

    /**
     * Scope : Demandes validées
     */
    public function scopeValidees($query)
    {
        return $query->where('statut', 'valide');
    }

    /**
     * Scope : Demandes rejetées
     */
    public function scopeRejetees($query)
    {
        return $query->where('statut', 'rejete');
    }

    /**
     * Scope : Demandes payées (boolean)
     */
    public function scopePaye($query)
    {
        return $query->where('paye', true);
    }

    /**
     * Scope : Demandes non payées
     */
    public function scopeNonPayees($query)
    {
        return $query->where('paye', false);
    }

    // ===== MÉTHODES =====

    /**
     * Valider la demande
     */
    public function valider(): bool
    {
        // ✅ Accepter 'en_attente' ET 'paye'
        if (!in_array($this->statut, ['en_attente', 'paye'])) {
            return false;
        }

        $this->update([
            'statut' => 'valide',
            'valide_le' => now(),
        ]);

        return true;
    }

    /**
     * Rejeter la demande
     */
    public function rejeter(string $motif): bool
    {
        // ✅ Accepter 'en_attente' ET 'paye'
        if (!in_array($this->statut, ['en_attente', 'paye'])) {
            return false;
        }

        $this->update([
            'statut' => 'rejete',
            'motif_rejet' => $motif,
        ]);

        return true;
    }

    /**
     * Marquer comme payée
     */
    public function marquerPayee(int $montant, int $paiementId): bool
    {
        $this->update([
            'statut' => 'paye',
            'paye' => true,
            'montant_paye' => $montant,
            'paiement_id' => $paiementId,
        ]);

        return true;
    }

    /**
     * Vérifier si un utilisateur peut demander un duplicata
     */
    public static function peutDemander(Certificat $certificat): bool
    {
        // Vérifier si une demande existe déjà (inclure 'paye')
        $demandeExistante = self::where('certificat_id', $certificat->id)
            ->whereIn('statut', ['en_attente', 'paye', 'valide'])
            ->exists();

        if ($demandeExistante) {
            return false;
        }

        // Vérifier si un duplicata existe déjà
        $duplicataExistant = Certificat::where('parent_id', $certificat->id)
            ->where('telecharge', false)
            ->exists();

        if ($duplicataExistant) {
            return false;
        }

        return true;
    }
}