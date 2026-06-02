<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $table = 'mails_log';

    protected $fillable = [
        'expediteur_id', 'destinataire_id', 'email_destinataire',
        'sujet', 'contenu', 'statut', 'date_envoi'
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
    ];

    public function expediteur()
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }
}