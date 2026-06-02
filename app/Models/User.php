<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'password',
        'avatar',
        'statut',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Nom complet
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Relations
    public function inscriptions()
    {
        return $this->hasMany(InscriptionFormation::class);
    }

    public function demandesService()
    {
        return $this->hasMany(DemandeService::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function ressources()
    {
        return $this->hasMany(Ressource::class, 'enseignant_id');
    }

    public function temoignages()
    {
        return $this->hasMany(Temoignage::class);
    }
}