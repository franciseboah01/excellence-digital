<?php

namespace App\Services;

use App\Mail\AdminClientMail;
use App\Mail\BienvenueMail;
use App\Mail\EnseignantApprenantMail;
use App\Models\MailLog;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailService
{
    /**
     * Envoyer un email et journaliser dans mails_log
     */
    public static function envoyer(
        string $view,
        string $sujet,
        string $emailDestinataire,
        array $data = [],
        ?int $expediteurId = null,
        ?int $destinataireId = null
    ): bool {
        try {
            Mail::send($view, $data, function ($message) use ($emailDestinataire, $sujet) {
                $message->to($emailDestinataire)->subject($sujet);
            });

            // Journaliser succès
            MailLog::create([
                'expediteur_id'     => $expediteurId,
                'destinataire_id'   => $destinataireId,
                'email_destinataire'=> $emailDestinataire,
                'sujet'             => $sujet,
                'contenu'           => json_encode($data),
                'statut'            => 'envoye',
                'date_envoi'        => now(),
            ]);

            return true;

        } catch (Throwable $e) {
            // Journaliser échec
            MailLog::create([
                'expediteur_id'     => $expediteurId,
                'destinataire_id'   => $destinataireId,
                'email_destinataire'=> $emailDestinataire,
                'sujet'             => $sujet,
                'contenu'           => $e->getMessage(),
                'statut'            => 'echoue',
                'date_envoi'        => now(),
            ]);

            return false;
        }
    }

    /**
     * Email de bienvenue
     */
    public static function bienvenue(User $user): void
    {
        try {
            Mail::to($user->email)->send(new BienvenueMail($user));
            MailLog::create([
                'destinataire_id'    => $user->id,
                'email_destinataire' => $user->email,
                'sujet'              => 'Bienvenue sur Excellence Digital Center !',
                'contenu'            => "Email de bienvenue envoyé à {$user->nom_complet}",
                'statut'             => 'envoye',
                'date_envoi'         => now(),
            ]);
        } catch (Throwable $e) {
            MailLog::create([
                'destinataire_id'    => $user->id,
                'email_destinataire' => $user->email,
                'sujet'              => 'Bienvenue — ÉCHEC',
                'contenu'            => $e->getMessage(),
                'statut'             => 'echoue',
                'date_envoi'         => now(),
            ]);
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
     */
    public static function enseignantVersApprenants(
        User $enseignant,
        array $apprenants,
        string $sujet,
        string $message
    ): int {
        $count = 0;
        foreach ($apprenants as $apprenant) {
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