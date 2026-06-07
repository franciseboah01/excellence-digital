<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionQcm extends Model
{
    protected $table = 'questions_qcm';

    protected $fillable = ['qcm_id', 'question', 'ordre', 'points'];

    public function qcm()
    {
        return $this->belongsTo(Qcm::class, 'qcm_id');
    }

    public function reponses()
    {
        return $this->hasMany(ReponseQcm::class, 'question_id')->orderBy('ordre');
    }

    public function reponsesCorrectes()
    {
        return $this->hasMany(ReponseQcm::class, 'question_id')
            ->where('est_correcte', true);
    }
}