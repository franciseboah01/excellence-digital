<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $fillable = [
        'titre', 'description', 'module_id',
        'duree', 'prix', 'image', 'statut'
    ];

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
}