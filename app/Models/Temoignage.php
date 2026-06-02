<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temoignage extends Model
{
    protected $fillable = [
        'user_id', 'contenu', 'note', 'statut_validation'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}