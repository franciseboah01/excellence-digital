<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Formation;
use App\Models\Qcm;
use App\Models\QuestionQcm;
use App\Models\ReponseQcm;
use App\Models\SessionQcm;
use App\Models\Certificat;
use App\Models\NiveauFormation;
use App\Models\InscriptionFormation;
use Spatie\Permission\Models\Role;

class CertificatsTestSeeder extends Seeder
{
    public function run(): void
    {
        $clientRole = Role::firstOrCreate(['name' => 'client']);

        // ===== CRÉER 3 CLIENTS =====
        $alice = User::firstOrCreate(
            ['email' => 'alice@test.ci'],
            ['nom' => 'Koné', 'prenom' => 'Alice', 'password' => bcrypt('password'), 'statut' => 'actif']
        );
        $alice->assignRole($clientRole);

        $bob = User::firstOrCreate(
            ['email' => 'bob@test.ci'],
            ['nom' => 'Traoré', 'prenom' => 'Bob', 'password' => bcrypt('password'), 'statut' => 'actif']
        );
        $bob->assignRole($clientRole);

        $carla = User::firstOrCreate(
            ['email' => 'carla@test.ci'],
            ['nom' => 'Konaté', 'prenom' => 'Carla', 'password' => bcrypt('password'), 'statut' => 'actif']
        );
        $carla->assignRole($clientRole);

        // ===== CRÉER UNE FORMATION =====
        $formation = Formation::firstOrCreate(
            ['titre' => 'Formation Excel Débutant'],
            [
                'description' => 'Apprenez les bases d\'Excel',
                'niveau'      => 'debutant',
                'duree'       => '2 semaines',
                'statut'      => 'publie',
            ]
        );

        // Niveau
        $niveau = NiveauFormation::firstOrCreate(
            ['formation_id' => $formation->id, 'nom' => 'Chapitre 1'],
            ['ordre' => 1, 'description' => 'Introduction']
        );

        // Inscrire les 3
        foreach ([$alice, $bob, $carla] as $user) {
            InscriptionFormation::firstOrCreate(
                ['user_id' => $user->id, 'formation_id' => $formation->id],
                ['statut' => 'valide', 'date_inscription' => now()]
            );
        }

        // ===== CRÉER UN QCM =====
        $qcm = Qcm::firstOrCreate(
            ['titre' => 'QCM Excel — Base', 'formation_id' => $formation->id],
            [
                'niveau_id'          => $niveau->id,
                'cree_par'           => User::role('admin')->first()->id ?? 1,
                'duree_par_question' => 120,
                'note_minimale'      => 14,
                'tentatives_max'     => 3,
                'actif'              => true,
            ]
        );

        // ===== CRÉER 5 QUESTIONS SI BESOIN =====
        if ($qcm->questions()->count() < 5) {
            $data = [
                ['Combien font 1+1 ?', ['1', '2', '3', '4'], [1]],
                ['Combien font 2+2 ?', ['2', '3', '4', '5'], [2]],
                ['Combien font 3+3 ?', ['4', '5', '6', '7'], [2]],
                ['Combien font 4+4 ?', ['6', '7', '8', '9'], [2]],
                ['Combien font 5+5 ?', ['8', '9', '10', '11'], [2]],
            ];
            foreach ($data as $i => $d) {
                $qst = QuestionQcm::create(['qcm_id' => $qcm->id, 'question' => $d[0], 'points' => 2, 'ordre' => $i + 1]);
                foreach ($d[1] as $j => $r) {
                    ReponseQcm::create(['question_id' => $qst->id, 'contenu' => $r, 'est_correcte' => in_array($j, $d[2]), 'ordre' => $j]);
                }
            }
        }

        // ===== SIMULER 3 SESSIONS =====

        // Alice — ÉCHEC (note 0/20)
        $this->creerSession($qcm, $alice, 0, 0, 10, 0, false);

        // Bob — RÉUSSITE + certificat téléchargé
        $sessionBob = $this->creerSession($qcm, $bob, 5, 10, 10, 20, true);

        Certificat::create([
            'user_id'           => $bob->id,
            'formation_id'      => $formation->id,
            'session_qcm_id'    => $sessionBob->id,
            'numero_certificat' => Certificat::genererNumero(),
            'note_obtenue'      => 20,
            'delivre_le'        => now()->subDays(2),
            'telecharge'        => true, // ✅ Déjà imprimé
        ]);

        // Carla — RÉUSSITE + certificat NON téléchargé
        $sessionCarla = $this->creerSession($qcm, $carla, 5, 10, 10, 20, true);

        Certificat::create([
            'user_id'           => $carla->id,
            'formation_id'      => $formation->id,
            'session_qcm_id'    => $sessionCarla->id,
            'numero_certificat' => Certificat::genererNumero(),
            'note_obtenue'      => 20,
            'delivre_le'        => now()->subDay(),
            'telecharge'        => false, // ❌ Pas encore imprimé
        ]);

        echo "✅ 3 utilisateurs de test créés :\n";
        echo "  - alice@test.ci (échoué)\n";
        echo "  - bob@test.ci (réussi + certificat téléchargé)\n";
        echo "  - carla@test.ci (réussi + certificat NON téléchargé)\n";
        echo "Mot de passe : password\n";
    }

    private function creerSession($qcm, $user, $bonnesReponses, $totalQuestions, $score, $scoreMax, $reussi)
    {
        $detail = [];
        foreach ($qcm->questions as $q) {
            $detail[$q->id] = [
                'donnees'   => $bonnesReponses > 0 ? [$q->reponsesCorrectes->first()->id] : [$q->reponses->first()->id],
                'correctes' => [$q->reponsesCorrectes->first()->id],
                'correct'   => $bonnesReponses > 0,
                'points'    => $bonnesReponses > 0 ? 2 : 0,
            ];
            $bonnesReponses--;
        }

        return SessionQcm::create([
            'qcm_id'           => $qcm->id,
            'user_id'          => $user->id,
            'reponses_donnees' => $detail,
            'score'            => $score,
            'score_max'        => $scoreMax,
            'note'             => $reussi ? 20 : 0,
            'reussi'           => $reussi,
            'tentative'        => 1,
            'debut_le'         => now()->subMinutes(10),
            'fin_le'           => now(),
        ]);
    }
}