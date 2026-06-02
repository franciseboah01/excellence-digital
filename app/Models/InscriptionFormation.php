<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscriptionFormation extends Model
{
    protected $table = 'inscriptions_formation';

    protected $fillable = [
        'user_id', 'formation_id', 'statut', 'date_inscription'
    ];

    protected $casts = [
        'date_inscription' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
}