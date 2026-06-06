<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'expediteur_id',
        'destinataire_id',
        'contenu',
        'lu',
        'lu_le',
    ];

    protected $casts = [
        'lu'    => 'boolean',
        'lu_le' => 'datetime',
    ];

    public function expediteur()
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    // Scope messages non lus
    public function scopeNonLus($query)
    {
        return $query->where('lu', false);
    }
}