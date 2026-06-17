<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'module_id',
        'duree',
        'prix',
        'places_max', // ← AJOUTÉ
        'image',
        'statut'
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'places_max' => 'integer',
    ];

    // ===== RELATIONS =====

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function niveaux()
    {
        return $this->hasMany(NiveauFormation::class);
    }

    public function inscriptions()
    {
        return $this->hasMany(InscriptionFormation::class);
    }

    public function ressources()
    {
        return $this->hasMany(Ressource::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'formation_id');
    }

    public function certificats()
    {
        return $this->hasMany(Certificat::class);
    }

    // ===== ACCESSORS =====

    /**
     * Vérifier si la formation est payante
     */
    public function getEstPayanteAttribute(): bool
    {
        return $this->prix && $this->prix > 0;
    }

    /**
     * Nombre de places disponibles
     */
    public function getPlacesDisponiblesAttribute(): int
    {
        if (!$this->places_max) {
            return PHP_INT_MAX;
        }

        $inscrits = InscriptionFormation::where('formation_id', $this->id)
            ->where('statut', 'valide')
            ->count();

        return max(0, $this->places_max - $inscrits);
    }

    /**
     * Vérifier si la formation est complète
     */
    public function getEstCompleteAttribute(): bool
    {
        return $this->places_max && $this->places_disponibles <= 0;
    }

    // ===== SCOPES =====

    /**
     * Scope : Formations publiées uniquement
     */
    public function scopePublie($query)
    {
        return $query->where('statut', 'publie');
    }

    /**
     * Scope : Formations avec places disponibles
     */
    public function scopeAvecPlaces($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('places_max')
              ->orWhereRaw('places_max > (SELECT COUNT(*) FROM inscriptions_formations WHERE formations.id = inscriptions_formations.formation_id AND statut = "valide")');
        });
    }

    /**
     * Scope : Formations gratuites
     */
    public function scopeGratuites($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('prix')->orWhere('prix', '<=', 0);
        });
    }

    /**
     * Scope : Formations payantes
     */
    public function scopePayantes($query)
    {
        return $query->where('prix', '>', 0);
    }

    // ===== MÉTHODES =====

    /**
     * Vérifier si un utilisateur est inscrit
     */
    public function estInscrit($userId): bool
    {
        return $this->inscriptions()
            ->where('user_id', $userId)
            ->where('statut', 'valide')
            ->exists();
    }

    /**
     * Vérifier si un utilisateur a payé
     */
    public function aPaye($userId): bool
    {
        return $this->paiements()
            ->where('user_id', $userId)
            ->where('statut', 'complete')
            ->exists();
    }
}