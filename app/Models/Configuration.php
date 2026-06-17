<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuration extends Model
{
    protected $fillable = ['cle', 'valeur', 'description'];

    /**
     * Récupérer une valeur de configuration
     */
    public static function get(string $cle, mixed $defaut = null): mixed
    {
        return Cache::remember("config_{$cle}", 3600, function () use ($cle, $defaut) {
            $config = static::where('cle', $cle)->first();
            return $config ? $config->valeur : $defaut;
        });
    }

    /**
     * Définir une valeur de configuration
     */
    public static function set(string $cle, mixed $valeur, string $description = ''): void
    {
        static::updateOrCreate(
            ['cle' => $cle],
            ['valeur' => $valeur, 'description' => $description]
        );
        Cache::forget("config_{$cle}");
    }

    /**
     * Vérifier si une clé de configuration existe et n'est pas vide
     */
    public static function has(string $cle): bool
    {
        $valeur = static::get($cle);
        return !empty($valeur);
    }
}