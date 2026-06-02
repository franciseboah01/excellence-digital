<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ressource extends Model
{
    protected $fillable = [
        'formation_id', 'enseignant_id', 'niveau_id',
        'type', 'titre', 'description',
        'fichier_path', 'lien_url', 'actif'
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function niveau()
    {
        return $this->belongsTo(NiveauFormation::class, 'niveau_id');
    }
}