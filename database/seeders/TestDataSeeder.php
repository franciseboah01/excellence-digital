<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\NiveauFormation;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ===== SERVICES =====
        $services = [
            ['titre' => 'Création de CV professionnel', 'description' => 'CV moderne et attractif adapté à votre profil.', 'categorie' => 'bureautique', 'prix' => 2000, 'icone' => '📄'],
            ['titre' => 'Mise en page Word', 'description' => 'Rapports, mémoires, dossiers professionnels.', 'categorie' => 'bureautique', 'prix' => 1500, 'icone' => '📝'],
            ['titre' => 'Tableau de bord Excel', 'description' => 'Suivi d\'activités et tableaux automatisés.', 'categorie' => 'bureautique', 'prix' => 5000, 'icone' => '📊'],
            ['titre' => 'Création de logo', 'description' => 'Logo professionnel adapté à votre activité.', 'categorie' => 'design', 'prix' => 10000, 'icone' => '🎨'],
            ['titre' => 'Affiche publicitaire', 'description' => 'Design attractif pour votre communication.', 'categorie' => 'design', 'prix' => 5000, 'icone' => '🖼️'],
            ['titre' => 'Site vitrine', 'description' => 'Site internet professionnel pour votre activité.', 'categorie' => 'web_mobile', 'prix' => 150000, 'icone' => '🌐'],
            ['titre' => 'Boutique en ligne', 'description' => 'E-commerce complet avec paiement intégré.', 'categorie' => 'web_mobile', 'prix' => 300000, 'icone' => '🛒'],
        ];

        foreach ($services as $s) {
            Service::firstOrCreate(['titre' => $s['titre']], array_merge($s, ['actif' => true]));
        }

        // ===== FORMATIONS =====
        $formations = [
            ['titre' => 'Formation Word', 'description' => 'Maîtrisez Word de A à Z.', 'niveau' => 'debutant', 'duree' => '2 semaines', 'statut' => 'publie'],
            ['titre' => 'Formation Excel', 'description' => 'Excel pour le travail et les affaires.', 'niveau' => 'intermediaire', 'duree' => '3 semaines', 'statut' => 'publie'],
            ['titre' => 'Formation PowerPoint', 'description' => 'Créez des présentations professionnelles.', 'niveau' => 'debutant', 'duree' => '1 semaine', 'statut' => 'publie'],
            ['titre' => 'Création de site web', 'description' => 'Apprenez à créer votre site web.', 'niveau' => 'avance', 'duree' => '4 semaines', 'statut' => 'publie'],
        ];

        foreach ($formations as $f) {
            $formation = Formation::firstOrCreate(['titre' => $f['titre']], $f);

            // Créer des niveaux pour chaque formation
            $niveaux = [
                ['nom' => 'Introduction', 'ordre' => 1, 'description' => 'Bases et prise en main'],
                ['nom' => 'Intermédiaire', 'ordre' => 2, 'description' => 'Fonctionnalités avancées'],
                ['nom' => 'Avancé', 'ordre' => 3, 'description' => 'Maîtrise complète'],
            ];

            foreach ($niveaux as $n) {
                NiveauFormation::firstOrCreate(
                    ['formation_id' => $formation->id, 'nom' => $n['nom']],
                    array_merge($n, ['formation_id' => $formation->id])
                );
            }
        }

        // ===== COMPTE ENSEIGNANT TEST =====
        $enseignant = User::firstOrCreate(
            ['email' => 'enseignant@excellencedigital.ci'],
            [
                'nom'               => 'KONÉ',
                'prenom'            => 'Ibrahim',
                'email'             => 'enseignant@excellencedigital.ci',
                'password'          => Hash::make('Enseignant@2024!'),
                'statut'            => 'actif',
                'email_verified_at' => now(),
            ]
        );
        $enseignant->assignRole('enseignant');

        // ===== COMPTE CLIENT TEST =====
        $client = User::firstOrCreate(
            ['email' => 'client@excellencedigital.ci'],
            [
                'nom'               => 'COULIBALY',
                'prenom'            => 'Aminata',
                'email'             => 'client@excellencedigital.ci',
                'password'          => Hash::make('Client@2024!'),
                'statut'            => 'actif',
                'email_verified_at' => now(),
            ]
        );
        $client->assignRole('client');

        // Inscrire le client à une formation
        $formation = Formation::where('titre', 'Formation Excel')->first();
        if ($formation) {
            InscriptionFormation::firstOrCreate(
                ['user_id' => $client->id, 'formation_id' => $formation->id],
                ['statut' => 'valide']
            );
        }

        $this->command->info('✅ Données de test créées !');
        $this->command->info('👤 Admin      : admin@excellencedigital.ci / Admin@2024!');
        $this->command->info('🎓 Enseignant : enseignant@excellencedigital.ci / Enseignant@2024!');
        $this->command->info('👤 Client     : client@excellencedigital.ci / Client@2024!');
    }
}