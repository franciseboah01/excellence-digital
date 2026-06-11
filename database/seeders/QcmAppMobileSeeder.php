<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Qcm;
use App\Models\QuestionQcm;
use App\Models\ReponseQcm;

class QcmAppMobileSeeder extends Seeder
{
    public function run(): void
    {
        $qcm = Qcm::create([
            'formation_id'       => 6,
            'cree_par'           => 1,
            'titre'              => "QCM — Création d'application mobile",
            'description'        => "Testez vos connaissances sur la création d'applications mobiles",
            'duree_par_question' => 120,
            'bareme'             => 20,
            'note_minimale'      => 14,
            'tentatives_max'     => 3,
            'actif'              => true,
        ]);

        $questions = [
            [
                'question' => 'Quel langage est utilisé pour développer des applications Android natives ?',
                'points' => 2,
                'reponses' => [
                    ['Java', true],
                    ['Python', false],
                    ['JavaScript', false],
                    ['PHP', false],
                ]
            ],
            [
                'question' => "Qu'est-ce que Flutter ?",
                'points' => 2,
                'reponses' => [
                    ['Un framework de Google pour le développement mobile', true],
                    ['Un langage de programmation', false],
                    ["Un système d'exploitation mobile", false],
                    ['Une base de données', false],
                ]
            ],
            [
                'question' => 'Quel langage utilise Flutter ?',
                'points' => 2,
                'reponses' => [
                    ['Dart', true],
                    ['Swift', false],
                    ['C++', false],
                    ['Ruby', false],
                ]
            ],
            [
                'question' => 'Quel IDE est officiellement supporté pour Android ?',
                'points' => 2,
                'reponses' => [
                    ['Android Studio', true],
                    ['Visual Studio Code', false],
                    ['Notepad++', false],
                    ['Sublime Text', false],
                ]
            ],
            [
                'question' => "Qu'est-ce qu'une API REST ?",
                'points' => 2,
                'reponses' => [
                    ['Une interface de communication entre applications', true],
                    ['Un langage de programmation', false],
                    ['Un type de base de données', false],
                    ['Un framework JavaScript', false],
                ]
            ],
            [
                'question' => 'Quel est le langage principal pour iOS ?',
                'points' => 2,
                'reponses' => [
                    ['Swift', true],
                    ['Kotlin', false],
                    ['Dart', false],
                    ['Java', false],
                ]
            ],
            [
                'question' => "Qu'est-ce que React Native ?",
                'points' => 2,
                'reponses' => [
                    ['Un framework JavaScript pour applications mobiles', true],
                    ["Un système d'exploitation", false],
                    ['Un langage de programmation', false],
                    ['Un IDE', false],
                ]
            ],
            [
                'question' => 'Quel format de données est couramment utilisé dans les API ?',
                'points' => 2,
                'reponses' => [
                    ['JSON', true],
                    ['XML', false],
                    ['CSV', false],
                    ['YAML', false],
                ]
            ],
            [
                'question' => "Qu'est-ce que le responsive design ?",
                'points' => 2,
                'reponses' => [
                    ["Une adaptation de l'interface à toutes les tailles d'écran", true],
                    ['Un langage de programmation', false],
                    ['Un framework', false],
                    ['Une base de données', false],
                ]
            ],
            [
                'question' => 'Quel outil permet de gérer les versions de code source ?',
                'points' => 2,
                'reponses' => [
                    ['Git', true],
                    ['Docker', false],
                    ['Jenkins', false],
                    ['Kubernetes', false],
                ]
            ],
        ];

        foreach ($questions as $index => $q) {
            $question = QuestionQcm::create([
                'qcm_id'   => $qcm->id,
                'question' => $q['question'],
                'points'   => $q['points'],
                'ordre'    => $index + 1,
            ]);

            foreach ($q['reponses'] as $ordre => $r) {
                ReponseQcm::create([
                    'question_id' => $question->id,
                    'contenu'     => $r[0],
                    'est_correcte'=> $r[1],
                    'ordre'       => $ordre,
                ]);
            }
        }

        echo "✅ QCM App Mobile créé avec 10 questions !\n";
    }
}