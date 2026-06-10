<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $fillable = ['nom', 'icone', 'actif'];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}