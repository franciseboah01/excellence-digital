<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temoignage extends Model
{
    protected $fillable = [
        'user_id', 'contenu', 'note',
        'formation_id', 'service_id',
        'statut_validation',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Étoiles HTML
    public function getEtoilesHtmlAttribute(): string
    {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= $i <= $this->note ? '★' : '☆';
        }
        return $html;
    }
}