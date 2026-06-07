<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Envoyer à un utilisateur spécifique
     */
    public static function envoyer(
        int $userId,
        string $titre,
        string $message,
        string $type = 'info',
        array $data = []
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'titre'   => e($titre),
            'message' => e($message),
            'type'    => $type,
            'data'    => $data,
        ]);
    }

    /**
     * Envoyer à tous les admins
     */
    public static function envoyerAdmins(
        string $titre,
        string $message,
        string $type = 'info'
    ): int {
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            static::envoyer($admin->id, $titre, $message, $type);
        }
        return $admins->count();
    }

    /**
     * Envoyer aux apprenants d'une formation
     */
    public static function envoyerFormation(
        Formation $formation,
        string $titre,
        string $message,
        string $type = 'info',
        array $data = []
    ): int {
        $count = 0;
        $formation->inscriptions()
            ->where('statut', 'valide')
            ->with('user')
            ->get()
            ->each(function ($inscription) use ($titre, $message, $type, $data, &$count) {
                static::envoyer($inscription->user_id, $titre, $message, $type, $data);
                $count++;
            });
        return $count;
    }

    /**
     * Diffusion générale
     */
    public static function diffuser(
        string $cible,
        string $titre,
        string $message,
        string $type = 'info'
    ): int {
        $query = User::query();

        match($cible) {
            'clients'      => $query->role('client'),
            'enseignants'  => $query->role('enseignant'),
            default        => $query->whereHas('roles', fn($q) =>
                                 $q->whereIn('name', ['client', 'enseignant'])
                             ),
        };

        $users = $query->where('statut', 'actif')->get();
        foreach ($users as $user) {
            static::envoyer($user->id, $titre, $message, $type, [
                'diffusion' => $cible,
            ]);
        }
        return $users->count();
    }
}