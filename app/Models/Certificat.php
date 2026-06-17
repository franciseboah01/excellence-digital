<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificat extends Model
{
    protected $fillable = [
        'user_id',
        'formation_id',
        'session_qcm_id',
        'parent_id', // ← AJOUTÉ
        'numero_certificat',
        'note_obtenue',
        'delivre_le',
        'telecharge',
        'verification_token',
    ];

    protected $casts = [
        'delivre_le' => 'datetime',
        'telecharge' => 'boolean',
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

    public function session()
    {
        return $this->belongsTo(SessionQcm::class, 'session_qcm_id');
    }

    /**
     * Certificat original (parent)
     */
    public function parent()
    {
        return $this->belongsTo(Certificat::class, 'parent_id');
    }

    /**
     * Duplicatas de ce certificat
     */
    public function duplicatas()
    {
        return $this->hasMany(Certificat::class, 'parent_id');
    }

    /**
     * Demandes de duplicata pour ce certificat
     */
    public function demandesDuplicata()
    {
        return $this->hasMany(DemandeDuplicata::class);
    }

    /**
     * Paiements associés à ce certificat
     */
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // ===== ACCESSORS =====

    /**
     * Vérifier si c'est un duplicata
     */
    public function getEstDuplicataAttribute(): bool
    {
        return str_ends_with($this->numero_certificat, '-DUP');
    }

    /**
     * Calculer la mention en fonction de la note
     */
    public function getMentionAttribute(): string
    {
        $note = $this->note_obtenue ?? 0;
        if ($note >= 18) return 'Très Bien';
        if ($note >= 16) return 'Bien';
        if ($note >= 14) return 'Assez Bien';
        if ($note >= 12) return 'Passable';
        return 'Insuffisant';
    }

    /**
     * Vérifier si le certificat est téléchargeable
     */
    public function getEstTelechargeableAttribute(): bool
    {
        // Original non encore téléchargé
        if (!$this->est_duplicata && !$this->telecharge) {
            return true;
        }

        // Duplicata validé et non encore téléchargé
        if ($this->est_duplicata && !$this->telecharge && $this->delivre_le) {
            return true;
        }

        return false;
    }

    // ===== SCOPES =====

    /**
     * Scope : Certificats originaux uniquement
     */
    public function scopeOriginaux($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope : Duplicatas uniquement
     */
    public function scopeDuplicatas($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Scope : Certificats téléchargeables
     */
    public function scopeTelechargeables($query)
    {
        return $query->where('telecharge', false);
    }

    /**
     * Scope : Certificats déjà téléchargés
     */
    public function scopeDejaTelecharges($query)
    {
        return $query->where('telecharge', true);
    }

    /**
     * Scope : Certificats validés (délivrés)
     */
    public function scopeValides($query)
    {
        return $query->whereNotNull('delivre_le');
    }

    // ===== MÉTHODES STATIQUES =====

    /**
     * Générer un numéro de certificat unique
     */
    public static function genererNumero(): string
    {
        return 'EDC-CERT-' . strtoupper(Str::random(8)) . '-' . date('Y');
    }

    /**
     * Générer un token de vérification unique
     */
    public static function genererToken(): string
    {
        return Str::random(32) . '-' . time();
    }

    // ===== ÉVÉNEMENTS =====

    protected static function boot()
    {
        parent::boot();

        // Générer automatiquement le token de vérification
        static::creating(function ($certificat) {
            if (empty($certificat->verification_token)) {
                $certificat->verification_token = self::genererToken();
            }
        });
    }
}