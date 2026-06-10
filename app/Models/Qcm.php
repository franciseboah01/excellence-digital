<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qcm extends Model
{
    protected $fillable = [
        'formation_id', 'niveau_id', 'cree_par',
        'titre', 'description', 'duree_par_question',
        'note_minimale', 'bareme', 'tentatives_max', 'actif',
    ];

    protected $casts = ['actif' => 'boolean'];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function niveau()
    {
        return $this->belongsTo(NiveauFormation::class, 'niveau_id');
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function questions()
    {
        return $this->hasMany(QuestionQcm::class, 'qcm_id')->orderBy('ordre');
    }

    public function sessions()
    {
        return $this->hasMany(SessionQcm::class, 'qcm_id');
    }

    public function getScoreMaxAttribute(): int
    {
        return $this->questions->sum('points');
    }

    public function getNombreQuestionsAttribute(): int
    {
        return $this->questions->count();
    }
}