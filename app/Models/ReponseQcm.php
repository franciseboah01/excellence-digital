<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReponseQcm extends Model
{
    protected $table = 'reponses_qcm';

    protected $fillable = ['question_id', 'contenu', 'est_correcte', 'ordre'];

    protected $casts = ['est_correcte' => 'boolean'];

    public function question()
    {
        return $this->belongsTo(QuestionQcm::class, 'question_id');
    }
}