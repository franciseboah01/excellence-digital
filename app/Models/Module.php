<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['nom', 'icone', 'actif'];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function formations()
    {
        return $this->hasMany(Formation::class);
    }
}