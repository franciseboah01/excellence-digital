<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'titre', 'description', 'categorie',
        'prix', 'icone', 'actif'
    ];

    public function demandes()
    {
        return $this->hasMany(DemandeService::class);
    }
}