<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeService extends Model
{
    protected $table = 'demandes_service';

    protected $fillable = [
        'user_id', 'service_id', 'nom_visiteur',
        'email_visiteur', 'telephone_visiteur',
        'message', 'fichiers', 'statut'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}