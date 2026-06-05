<?php

namespace App\Services;

use App\Mail\AdminClientMail;
use App\Mail\BienvenueMail;
use App\Mail\EnseignantApprenantMail;
use App\Models\MailLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailService
{
    /**
     * Email de bienvenue — retourne bool pour indiquer succès/échec
     */
    public static function bienvenue(User $user): bool  // FIX 6 : void → bool
    {
        try {
            Mail::to($user->email)->send(new BienvenueMail($user));

            MailLog::create([
                'destinataire_id'    => $user->id,
                'email_destinataire' => $user->email,
                'sujet'              => 'Bienvenue sur Excellence Digital Center !',
                // FIX 3 : accessor → attributs directs
                'contenu'            => "Email de bienvenue envoyé à {$user->prenom} {$user->nom}",
                'statut'             => 'envoye',
                'date_envoi'         => now(),
            ]);

            return true;

        } catch (Throwable $e) {
            MailLog::create([
                'destinataire_id'    => $user->id,
                'email_destinataire' => $user->email,
                'sujet'              => 'Bienvenue — ÉCHEC',
                'contenu'            => $e->getMessage(),
                'statut'             => 'echoue',
                'date_envoi'         => now(),
            ]);

            return false;
        }
    }

    /**
     * Email admin → client
     */
    public static function adminVersClient(
        User $admin,
        User $client,
        string $sujet,
        string $message
    ): bool {
        try {
            Mail::to($client->email)->send(
                new AdminClientMail($admin, $client, $sujet, $message)
            );

            MailLog::create([
                'expediteur_id'      => $admin->id,
                'destinataire_id'    => $client->id,
                'email_destinataire' => $client->email,
                'sujet'              => $sujet,
                'contenu'            => $message,
                'statut'             => 'envoye',
                'date_envoi'         => now(),
            ]);

            return true;

        } catch (Throwable $e) {
            MailLog::create([
                'expediteur_id'      => $admin->id,
                'destinataire_id'    => $client->id,
                'email_destinataire' => $client->email,
                'sujet'              => $sujet . ' — ÉCHEC',
                'contenu'            => $e->getMessage(),
                'statut'             => 'echoue',
                'date_envoi'         => now(),
            ]);

            return false;
        }
    }

    /**
     * Email enseignant → apprenants
     * FIX 7 : type hint Collection|array plus explicite
     */
    public static function enseignantVersApprenants(
        User $enseignant,
        Collection|array $apprenants,
        string $sujet,
        string $message
    ): int {
        $count = 0;

        foreach ($apprenants as $apprenant) {
            // Vérifier que c'est bien un objet User
            if (!$apprenant instanceof User) continue;

            try {
                Mail::to($apprenant->email)->send(
                    new EnseignantApprenantMail($enseignant, $apprenant, $sujet, $message)
                );

                MailLog::create([
                    'expediteur_id'      => $enseignant->id,
                    'destinataire_id'    => $apprenant->id,
                    'email_destinataire' => $apprenant->email,
                    'sujet'              => $sujet,
                    'contenu'            => $message,
                    'statut'             => 'envoye',
                    'date_envoi'         => now(),
                ]);

                $count++;

            } catch (Throwable $e) {
                MailLog::create([
                    'expediteur_id'      => $enseignant->id,
                    'destinataire_id'    => $apprenant->id,
                    'email_destinataire' => $apprenant->email,
                    'sujet'              => $sujet . ' — ÉCHEC',
                    'contenu'            => $e->getMessage(),
                    'statut'             => 'echoue',
                    'date_envoi'         => now(),
                ]);
            }
        }

        return $count;
    }
}