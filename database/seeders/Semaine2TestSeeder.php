<?php

namespace Database\Seeders;

use App\Models\DemandeService;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\Notification;
use App\Models\Ressource;
use App\Models\Service;
use App\Models\Temoignage;
use App\Models\User;
use Illuminate\Database\Seeder;

class Semaine2TestSeeder extends Seeder
{
    public function run(): void
    {
        $admin      = User::where('email', 'admin@excellencedigital.ci')->first();
        $enseignant = User::where('email', 'enseignant@excellencedigital.ci')->first();
        $client     = User::where('email', 'client@excellencedigital.ci')->first();

        if (!$admin || !$enseignant || !$client) {
            $this->command->error('❌ Comptes de test manquants. Lancez d\'abord TestDataSeeder.');
            return;
        }

        // ===== DEMANDES DE SERVICE =====
        $service = Service::first();
        if ($service) {
            DemandeService::firstOrCreate(
                ['email_visiteur' => 'visiteur@test.ci', 'service_id' => $service->id],
                [
                    'nom_visiteur'       => 'Koné Visiteur',
                    'email_visiteur'     => 'visiteur@test.ci',
                    'telephone_visiteur' => '+225 05 00 00 00 00',
                    'service_id'         => $service->id,
                    'message'            => 'Je voudrais un CV professionnel pour ma candidature.',
                    'statut'             => 'en_attente',
                ]
            );

            DemandeService::firstOrCreate(
                ['user_id' => $client->id, 'service_id' => $service->id],
                [
                    'user_id'    => $client->id,
                    'service_id' => $service->id,
                    'message'    => 'Mise en page de mon rapport de stage.',
                    'statut'     => 'en_cours',
                ]
            );
        }

        // ===== RESSOURCES ENSEIGNANT =====
        $formation = Formation::where('titre', 'Formation Excel')->first();
        if ($formation && $enseignant) {
            $niveaux = $formation->niveaux;

            if ($niveaux->count() > 0) {
                Ressource::firstOrCreate(
                    ['formation_id' => $formation->id, 'enseignant_id' => $enseignant->id, 'titre' => 'Introduction à Excel'],
                    [
                        'formation_id'  => $formation->id,
                        'enseignant_id' => $enseignant->id,
                        'niveau_id'     => $niveaux->first()->id,
                        'type'          => 'lien',
                        'titre'         => 'Introduction à Excel',
                        'description'   => 'Vidéo d\'introduction au tableur Excel.',
                        'lien_url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'actif'         => true,
                    ]
                );
            }
        }

        // ===== NOTIFICATIONS =====
        Notification::firstOrCreate(
            ['user_id' => $client->id, 'titre' => 'Bienvenue sur EDC !'],
            [
                'user_id' => $client->id,
                'titre'   => 'Bienvenue sur EDC !',
                'message' => 'Votre compte a été créé avec succès. Explorez nos formations !',
                'type'    => 'success',
                'lu'      => false,
            ]
        );

        Notification::firstOrCreate(
            ['user_id' => $client->id, 'titre' => 'Votre demande est en cours'],
            [
                'user_id' => $client->id,
                'titre'   => 'Votre demande est en cours',
                'message' => 'Notre équipe traite votre demande. Vous serez notifié dès qu\'elle est terminée.',
                'type'    => 'info',
                'lu'      => false,
            ]
        );

        Notification::firstOrCreate(
            ['user_id' => $enseignant->id, 'titre' => 'Nouvelle formation assignée'],
            [
                'user_id' => $enseignant->id,
                'titre'   => 'Nouvelle formation assignée',
                'message' => 'Vous avez été assigné à la formation "Formation Excel".',
                'type'    => 'info',
                'lu'      => false,
            ]
        );

        // ===== TÉMOIGNAGE =====
        Temoignage::firstOrCreate(
            ['user_id' => $client->id],
            [
                'user_id'            => $client->id,
                'contenu'            => 'Excellent centre ! Les formations sont très pratiques et les formateurs sont compétents.',
                'note'               => 5,
                'statut_validation'  => 'valide',
            ]
        );

        $this->command->info('✅ Données Semaine 2 créées !');
        $this->command->info('📊 Demandes, ressources, notifications et témoignages ajoutés.');
    }
}