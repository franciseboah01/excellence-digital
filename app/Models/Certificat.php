<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificat extends Model
{
    protected $fillable = [
        'user_id', 'formation_id', 'session_qcm_id',
        'numero_certificat', 'note_obtenue', 'delivre_le',
    ];

    protected $casts = ['delivre_le' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function session()
    {
        return $this->belongsTo(SessionQcm::class, 'session_qcm_id');
    }

    public static function genererNumero(): string
    {
        return 'EDC-CERT-' . strtoupper(Str::random(8)) . '-' . date('Y');
    }
}