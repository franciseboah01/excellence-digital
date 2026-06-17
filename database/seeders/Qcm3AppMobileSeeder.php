<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Qcm;
use App\Models\QuestionQcm;
use App\Models\ReponseQcm;

class Qcm3AppMobileSeeder extends Seeder
{
    public function run(): void
    {
        // ===== QCM 1 : Développement Android =====
        $qcm1 = Qcm::create([
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

        $questions1 = [
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

        foreach ($questions1 as $index => $q) {
            $question = QuestionQcm::create([
                'qcm_id'   => $qcm1->id,
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

        echo "✅ QCM 1 - App Mobile créé avec 10 questions !\n";

        // ===== QCM 2 : Développement Web =====
        $qcm2 = Qcm::create([
            'formation_id'       => 6,
            'cree_par'           => 1,
            'titre'              => "QCM — Développement Web Full Stack",
            'description'        => "Testez vos connaissances sur le développement Web",
            'duree_par_question' => 90,
            'bareme'             => 20,
            'note_minimale'      => 12,
            'tentatives_max'     => 3,
            'actif'              => true,
        ]);

        $questions2 = [
            [
                'question' => 'Quel langage est utilisé pour le développement front-end ?',
                'points' => 2,
                'reponses' => [
                    ['JavaScript', true],
                    ['Java', false],
                    ['C#', false],
                    ['Go', false],
                ]
            ],
            [
                'question' => 'Quel framework JavaScript est développé par Google ?',
                'points' => 2,
                'reponses' => [
                    ['Angular', true],
                    ['React', false],
                    ['Vue.js', false],
                    ['Svelte', false],
                ]
            ],
            [
                'question' => 'Quel langage est utilisé côté serveur avec Laravel ?',
                'points' => 2,
                'reponses' => [
                    ['PHP', true],
                    ['Python', false],
                    ['Ruby', false],
                    ['Node.js', false],
                ]
            ],
            [
                'question' => 'Quelle base de données est relationnelle ?',
                'points' => 2,
                'reponses' => [
                    ['MySQL', true],
                    ['MongoDB', false],
                    ['Firebase', false],
                    ['Redis', false],
                ]
            ],
            [
                'question' => 'Que signifie HTML ?',
                'points' => 2,
                'reponses' => [
                    ['HyperText Markup Language', true],
                    ['HighTech Markup Language', false],
                    ['HyperTransfer Markup Language', false],
                    ['HighLevel Markup Language', false],
                ]
            ],
            [
                'question' => 'Que signifie CSS ?',
                'points' => 2,
                'reponses' => [
                    ['Cascading Style Sheets', true],
                    ['Creative Style Sheets', false],
                    ['Computer Style System', false],
                    ['Cascading System Style', false],
                ]
            ],
            [
                'question' => 'Quel est le rôle de Node.js ?',
                'points' => 2,
                'reponses' => [
                    ['Exécuter du JavaScript côté serveur', true],
                    ['Créer des bases de données', false],
                    ['Développer des applications iOS', false],
                    ['Créer des interfaces graphiques', false],
                ]
            ],
            [
                'question' => 'Quel outil est utilisé pour le versionnement ?',
                'points' => 2,
                'reponses' => [
                    ['Git', true],
                    ['SVN', false],
                    ['Mercurial', false],
                    ['CVS', false],
                ]
            ],
            [
                'question' => 'Que signifie API ?',
                'points' => 2,
                'reponses' => [
                    ['Application Programming Interface', true],
                    ['Application Protocol Interface', false],
                    ['Advanced Programming Interface', false],
                    ['Automated Process Interface', false],
                ]
            ],
            [
                'question' => 'Quel est le port par défaut pour MySQL ?',
                'points' => 2,
                'reponses' => [
                    ['3306', true],
                    ['5432', false],
                    ['27017', false],
                    ['6379', false],
                ]
            ],
        ];

        foreach ($questions2 as $index => $q) {
            $question = QuestionQcm::create([
                'qcm_id'   => $qcm2->id,
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

        echo "✅ QCM 2 - Développement Web créé avec 10 questions !\n";

        // ===== QCM 3 : UI/UX Design =====
        $qcm3 = Qcm::create([
            'formation_id'       => 6,
            'cree_par'           => 1,
            'titre'              => "QCM — UI/UX Design",
            'description'        => "Testez vos connaissances sur le design d'interface et l'expérience utilisateur",
            'duree_par_question' => 100,
            'bareme'             => 20,
            'note_minimale'      => 14,
            'tentatives_max'     => 2,
            'actif'              => true,
        ]);

        $questions3 = [
            [
                'question' => "Que signifie UX ?",
                'points' => 2,
                'reponses' => [
                    ['User Experience', true],
                    ['User Exchange', false],
                    ['Universal Experience', false],
                    ['Unique Experience', false],
                ]
            ],
            [
                'question' => "Que signifie UI ?",
                'points' => 2,
                'reponses' => [
                    ['User Interface', true],
                    ['User Interaction', false],
                    ['Universal Interface', false],
                    ['Unique Interface', false],
                ]
            ],
            [
                'question' => "Quel outil est utilisé pour le prototypage ?",
                'points' => 2,
                'reponses' => [
                    ['Figma', true],
                    ['Photoshop', false],
                    ['Illustrator', false],
                    ['Premiere Pro', false],
                ]
            ],
            [
                'question' => "Qu'est-ce qu'un wireframe ?",
                'points' => 2,
                'reponses' => [
                    ['Une maquette basse fidélité d\'une interface', true],
                    ['Une maquette haute fidélité', false],
                    ['Un prototype interactif', false],
                    ['Un code source', false],
                ]
            ],
            [
                'question' => "Qu'est-ce que la hiérarchie visuelle ?",
                'points' => 2,
                'reponses' => [
                    ["L'organisation des éléments pour guider l'attention", true],
                    ['Le code CSS', false],
                    ['Le responsive design', false],
                    ['La typographie', false],
                ]
            ],
            [
                'question' => "Qu'est-ce qu'un design system ?",
                'points' => 2,
                'reponses' => [
                    ['Un ensemble de règles et composants réutilisables', true],
                    ['Un framework CSS', false],
                    ['Un langage de programmation', false],
                    ['Une base de données', false],
                ]
            ],
            [
                'question' => "Quelle est la différence entre UI et UX ?",
                'points' => 2,
                'reponses' => [
                    ['UI est l\'interface, UX est l\'expérience utilisateur', true],
                    ['UI est le design, UX est le code', false],
                    ['UI est pour mobile, UX pour desktop', false],
                    ['UI est pour les applications, UX pour les sites', false],
                ]
            ],
            [
                'question' => "Qu'est-ce que l'accessibilité en design ?",
                'points' => 2,
                'reponses' => [
                    ["La conception pour tous les utilisateurs, y compris handicapés", true],
                    ['Le code CSS', false],
                    ['Le responsive design', false],
                    ['Les animations', false],
                ]
            ],
            [
                'question' => "Quel outil est utilisé pour les tests utilisateurs ?",
                'points' => 2,
                'reponses' => [
                    ['UsabilityHub', true],
                    ['Figma', false],
                    ['Photoshop', false],
                    ['Illustrator', false],
                ]
            ],
            [
                'question' => "Qu'est-ce que le responsive design ?",
                'points' => 2,
                'reponses' => [
                    ["L'adaptation de l'interface à toutes les tailles d'écran", true],
                    ['Un langage de programmation', false],
                    ['Un framework', false],
                    ['Une police de caractères', false],
                ]
            ],
        ];

        foreach ($questions3 as $index => $q) {
            $question = QuestionQcm::create([
                'qcm_id'   => $qcm3->id,
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

        echo "✅ QCM 3 - UI/UX Design créé avec 10 questions !\n";
        echo "✅ Tous les QCMs ont été créés avec succès !\n";
    }
}