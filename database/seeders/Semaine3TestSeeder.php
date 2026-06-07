<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Configuration;
use App\Models\Faq;
use App\Models\Message;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Database\Seeder;

class Semaine3TestSeeder extends Seeder
{
    public function run(): void
    {
        $admin      = User::where('email', 'admin@excellencedigital.ci')->first();
        $client     = User::where('email', 'client@excellencedigital.ci')->first();
        $enseignant = User::where('email', 'enseignant@excellencedigital.ci')->first();

        if (!$admin || !$client) {
            $this->command->error('❌ Comptes de test manquants.');
            return;
        }

        // ===== MESSAGES =====
        Message::firstOrCreate(
            ['expediteur_id' => $admin->id, 'destinataire_id' => $client->id,
             'contenu' => 'Bonjour ! Comment puis-je vous aider ?'],
            ['expediteur_id' => $admin->id, 'destinataire_id' => $client->id,
             'contenu' => 'Bonjour ! Comment puis-je vous aider ?', 'lu' => false]
        );

        Message::firstOrCreate(
            ['expediteur_id' => $client->id, 'destinataire_id' => $admin->id,
             'contenu' => 'Bonjour, j\'ai une question sur la formation Excel.'],
            ['expediteur_id' => $client->id, 'destinataire_id' => $admin->id,
             'contenu' => 'Bonjour, j\'ai une question sur la formation Excel.', 'lu' => false]
        );

        // ===== FAQ =====
        $faqs = [
            ['question' => 'Comment s\'inscrire à une formation ?',
             'reponse'  => 'Créez un compte, connectez-vous et choisissez votre formation depuis votre espace client.',
             'categorie'=> 'formations', 'ordre' => 1],
            ['question' => 'Quels modes de paiement acceptez-vous ?',
             'reponse'  => 'Nous acceptons les espèces, Mobile Money et les virements bancaires.',
             'categorie'=> 'paiements', 'ordre' => 1],
            ['question' => 'Les formations sont-elles disponibles en ligne ?',
             'reponse'  => 'Oui, nous proposons des formations en ligne et en présentiel à Korhogo / Sirasso.',
             'categorie'=> 'formations', 'ordre' => 2],
            ['question' => 'Comment contacter un formateur ?',
             'reponse'  => 'Utilisez la messagerie interne depuis votre espace client après inscription.',
             'categorie'=> 'général', 'ordre' => 1],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                array_merge($faq, ['actif' => true])
            );
        }

        // ===== ARTICLES =====
        Article::firstOrCreate(
            ['slug' => 'bienvenue-sur-excellence-digital-center'],
            [
                'auteur_id' => $admin->id,
                'titre'     => 'Bienvenue sur Excellence Digital Center !',
                'slug'      => 'bienvenue-sur-excellence-digital-center',
                'extrait'   => 'Découvrez nos services bureautiques, formations pratiques et solutions digitales.',
                'contenu'   => "Excellence Digital Center est votre partenaire de confiance pour tous vos besoins en bureautique, digital et formation.\n\nNous proposons des services de qualité adaptés aux particuliers, étudiants, entrepreneurs et entreprises de Korhogo et de Sirasso.\n\nN'hésitez pas à nous contacter pour découvrir comment nous pouvons vous aider à Former, Créer et Réussir !",
                'categorie' => 'actualite',
                'statut'    => 'publie',
                'publie_le' => now(),
            ]
        );

        Article::firstOrCreate(
            ['slug' => '5-conseils-pour-maitriser-excel'],
            [
                'auteur_id' => $admin->id,
                'titre'     => '5 Conseils pour maîtriser Excel rapidement',
                'slug'      => '5-conseils-pour-maitriser-excel',
                'extrait'   => 'Excel est un outil puissant. Voici 5 conseils pratiques pour progresser rapidement.',
                'contenu'   => "1. Commencez par les formules de base : SOMME, MOYENNE, SI\n\n2. Apprenez les raccourcis clavier essentiels\n\n3. Utilisez les tableaux croisés dynamiques\n\n4. Maîtrisez la mise en forme conditionnelle\n\n5. Pratiquez avec des données réelles\n\nNotre formation Excel vous guidera pas à pas vers la maîtrise complète du tableur !",
                'categorie' => 'conseil',
                'statut'    => 'publie',
                'publie_le' => now()->subDays(2),
            ]
        );

        // ===== PAIEMENT TEST =====
        if ($client) {
            Paiement::firstOrCreate(
                ['reference' => 'EDC-TEST001'],
                [
                    'user_id'        => $client->id,
                    'montant_total'  => 50000,
                    'montant_paye'   => 25000,
                    'statut'         => 'partiel',
                    'mode_paiement'  => 'mobile_money',
                    'reference'      => 'EDC-TEST001',
                    'notes'          => 'Paiement test — acompte 50%',
                    'enregistre_par' => $admin->id,
                    'date_paiement'  => now(),
                ]
            );
        }

        $this->command->info('✅ Données Semaine 3 créées !');
        $this->command->info('💬 Messages, FAQ, Articles, Paiement test ajoutés.');
    }
}