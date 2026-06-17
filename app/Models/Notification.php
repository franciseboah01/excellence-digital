<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'titre',
        'message',
        'lien', // ← AJOUTÉ
        'type',
        'lu',
        'data'
    ];

    protected $casts = [
        'lu'   => 'boolean',
        'data' => 'array',
    ];

    // ===== RELATIONS =====

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ===== SCOPES =====

    /**
     * Scope : Notifications non lues
     */
    public function scopeNonLues($query)
    {
        return $query->where('lu', false);
    }

    /**
     * Scope : Notifications lues
     */
    public function scopeLues($query)
    {
        return $query->where('lu', true);
    }

    /**
     * Scope : Notifications par type
     */
    public function scopeDeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope : Notifications récentes
     */
    public function scopeRecentes($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    // ===== MÉTHODES =====

    /**
     * Marquer comme lue
     */
    public function marquerCommeLue(): bool
    {
        if ($this->lu) {
            return false;
        }

        $this->update(['lu' => true]);
        return true;
    }

    /**
     * Marquer toutes les notifications d'un utilisateur comme lues
     */
    public static function marquerToutCommeLu($userId): int
    {
        return self::where('user_id', $userId)
            ->where('lu', false)
            ->update(['lu' => true]);
    }

    /**
     * Compter les notifications non lues d'un utilisateur
     */
    public static function compterNonLues($userId): int
    {
        return self::where('user_id', $userId)
            ->where('lu', false)
            ->count();
    }
}