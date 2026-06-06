<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['question', 'reponse', 'categorie', 'ordre', 'actif'];

    protected $casts = ['actif' => 'boolean'];
}