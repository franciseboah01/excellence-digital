<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $fillable = [
        'titre', 'description', 'niveau',
        'duree', 'image', 'statut'
    ];

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
}