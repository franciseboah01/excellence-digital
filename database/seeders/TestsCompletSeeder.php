<?php

namespace Database\Seeders;

use App\Models\DemandeService;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Paiement;
use App\Models\Ressource;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestsCompletSeeder extends Seeder
{
    public function run(): void
    {
        $admin      = User::where('email', 'admin@excellencedigital.ci')->first();
        $enseignant = User::where('email', 'enseignant@excellencedigital.ci')->first();
        $client     = User::where('email', 'client@excellencedigital.ci')->first();

        // Vérifications
        if (!$admin || !$enseignant || !$client) {
            $this->command->error('❌ Comptes manquants. Lancez d\'abord TestDataSeeder.');
            return;
        }

        $formation = Formation::where('titre', 'Formation Excel')->first();
        $service   = Service::first();

        // ===== S'assurer que l'inscription est validée =====
        if ($formation) {
            InscriptionFormation::updateOrCreate(
                ['user_id' => $client->id, 'formation_id' => $formation->id],
                ['statut' => 'valide']
            );
        }

        // ===== S'assurer que la formation est publiée =====
        if ($formation) {
            $formation->update(['statut' => 'publie']);
        }

        // ===== Ressource de test =====
        if ($formation && $enseignant) {
            Ressource::firstOrCreate(
                ['titre' => 'Lien de test', 'formation_id' => $formation->id],
                [
                    'formation_id'  => $formation->id,
                    'enseignant_id' => $enseignant->id,
                    'type'          => 'lien',
                    'titre'         => 'Lien de test',
                    'lien_url'      => 'https://www.youtube.com/watch?v=test',
                    'actif'         => true,
                ]
            );
        }

        // ===== Messages =====
        Message::firstOrCreate(
            ['expediteur_id' => $admin->id, 'destinataire_id' => $client->id,
             'contenu' => 'Bonjour, votre inscription a été validée !'],
            ['expediteur_id' => $admin->id, 'destinataire_id' => $client->id,
             'contenu' => 'Bonjour, votre inscription a été validée !', 'lu' => false]
        );

        // ===== Notifications complètes =====
        foreach ([
            ['user_id' => $client->id, 'titre' => '✅ Inscription validée !',
             'message' => 'Votre inscription à Formation Excel est validée.', 'type' => 'success'],
            ['user_id' => $client->id, 'titre' => '📚 Nouvelle ressource disponible',
             'message' => 'Un nouveau cours a été ajouté à votre formation.', 'type' => 'info'],
            ['user_id' => $enseignant->id, 'titre' => '👥 Nouvel apprenant',
             'message' => 'Un nouveau client s\'est inscrit à Formation Excel.', 'type' => 'info'],
        ] as $notif) {
            Notification::firstOrCreate(
                ['user_id' => $notif['user_id'], 'titre' => $notif['titre']],
                array_merge($notif, ['lu' => false])
            );
        }

        // ===== Paiement complet =====
        if ($service) {
            Paiement::firstOrCreate(
                ['reference' => 'EDC-COMPLET01'],
                [
                    'user_id'        => $client->id,
                    'service_id'     => $service->id,
                    'montant_total'  => 5000,
                    'montant_paye'   => 5000,
                    'statut'         => 'complete',
                    'mode_paiement'  => 'especes',
                    'reference'      => 'EDC-COMPLET01',
                    'enregistre_par' => $admin->id,
                    'date_paiement'  => now()->subDays(3),
                ]
            );
        }

        $this->command->info('✅ Données de tests complets créées !');
    }
}