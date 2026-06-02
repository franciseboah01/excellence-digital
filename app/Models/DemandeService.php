<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeService extends Model
{
    protected $table = 'demandes_service';

    protected $fillable = [
        'user_id',
        'service_id',
        'nom_visiteur',
        'email_visiteur',
        'telephone_visiteur',
        'message',
        'fichiers',
        'statut',
    ];

    // ✅ CORRECTION 5 : relation user avec foreignKey explicite
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}