<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionQcm extends Model
{
    protected $table = 'sessions_qcm';

    protected $fillable = [
        'qcm_id', 'user_id', 'reponses_donnees',
        'score', 'score_max', 'note', 'reussi',
        'tentative', 'debut_le', 'fin_le',
    ];

    protected $casts = [
        'reponses_donnees' => 'array',
        'reussi'           => 'boolean',
        'debut_le'         => 'datetime',
        'fin_le'           => 'datetime',
    ];

    public function qcm()
    {
        return $this->belongsTo(Qcm::class, 'qcm_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function certificat()
    {
        return $this->hasOne(Certificat::class, 'session_qcm_id');
    }
}