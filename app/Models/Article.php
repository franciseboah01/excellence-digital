<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'auteur_id', 'titre', 'slug', 'extrait',
        'contenu', 'image', 'categorie',
        'statut', 'publie_le', 'vues',
    ];

    protected $casts = [
        'publie_le' => 'datetime',
    ];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    // Générer slug automatiquement
    public static function genererSlug(string $titre): string
    {
        $slug = Str::slug($titre);
        $count = static::where('slug', 'like', "{$slug}%")->count();
        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }

    // Incrémenter vues
    public function incrementerVues(): void
    {
        $this->increment('vues');
    }
}