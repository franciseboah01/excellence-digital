<?php

namespace App\Services;

use App\Mail\DemandeServiceMail;
use App\Models\DemandeService as DemandeModel;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class DemandeService
{
    /**
     * Créer une demande et envoyer les notifications
     */
    public static function creer(array $data): DemandeModel
    {
        $demande = DemandeModel::create(array_merge($data, [
            'statut' => 'en_attente',
        ]));

        // Email de confirmation
        try {
            Mail::to($data['email_visiteur'])
                ->send(new DemandeServiceMail($demande));
        } catch (\Throwable $e) {
            \Log::warning("Email demande non envoyé : {$e->getMessage()}");
        }

        // Notification admins
        User::role('admin')->each(function ($admin) use ($demande) {
            Notification::create([
                'user_id' => $admin->id,
                'titre'   => '🔔 Nouvelle demande de service',
                'message' => "Demande de " . e($demande->nom_visiteur ?? $demande->user?->prenom) .
                             " pour : " . e($demande->service->titre),
                'type'    => 'info',
            ]);
        });

        return $demande;
    }

    /**
     * Transitions d'état autorisées
     */
    public static array $transitions = [
        'en_attente' => ['en_cours', 'annule'],
        'en_cours'   => ['termine', 'annule'],
        'termine'    => [],
        'annule'     => [],
    ];

    public static function transitionAutorisee(string $actuel, string $nouveau): bool
    {
        return in_array($nouveau, static::$transitions[$actuel] ?? []);
    }
}