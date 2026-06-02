<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NiveauFormation extends Model
{
    protected $table = 'niveaux_formation';

    protected $fillable = [
        'formation_id', 'nom', 'ordre', 'description'
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function ressources()
    {
        return $this->hasMany(Ressource::class, 'niveau_id');
    }
}