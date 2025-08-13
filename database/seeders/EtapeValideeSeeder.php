<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EtapeValideeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $validations = [
            // Validations pour Jean Kimbangu (ame_id: 1)
            [
                'ame_id' => 1,
                'parcours_spirituel_id' => 1, // Découverte de la Foi
                'valide_par' => 2, // Pasteur Nkounkou
                'date_validation' => Carbon::create(2024, 2, 15),
                'commentaires' => 'Très bonnes bases acquises. Prêt pour l\'étape suivante.',
            ],
            [
                'ame_id' => 1,
                'parcours_spirituel_id' => 2, // Fondements Chrétiens
                'valide_par' => 4, // Soeur Loubaki
                'date_validation' => Carbon::create(2024, 3, 20),
                'commentaires' => 'Examen réussi avec 85%. Bonne compréhension des doctrines.',
            ],

            // Validations pour Marcelline Nkounkou (ame_id: 3)
            [
                'ame_id' => 3,
                'parcours_spirituel_id' => 1, // Découverte de la Foi
                'valide_par' => 5, // David Matsiona
                'date_validation' => Carbon::create(2024, 3, 12),
                'commentaires' => 'Conversion sincère. À suivre de près.',
            ],

            // Validations pour Grâce Okombi (ame_id: 5)
            [
                'ame_id' => 5,
                'parcours_spirituel_id' => 1, // Découverte de la Foi
                'valide_par' => 7, // Pasteur Itoua
                'date_validation' => Carbon::create(2024, 4, 22),
                'commentaires' => 'Baptême prévu le mois prochain.',
            ],
            [
                'ame_id' => 5,
                'parcours_spirituel_id' => 6, // École du Dimanche
                'valide_par' => 6, // Sarah Bouanga
                'date_validation' => Carbon::create(2024, 5, 10),
                'commentaires' => 'Assidue aux cours. Participation active.',
            ],

            // Validation partielle pour Jonathan Itoua (ame_id: 6)
            [
                'ame_id' => 6,
                'parcours_spirituel_id' => 1, // Découverte de la Foi
                'valide_par' => null, // Pas encore validé
                'date_validation' => null,
                'commentaires' => 'En cours de formation. Quelques difficultés à surmonter.',
            ],
        ];

        DB::table('etapes_validees')->insert($validations);
    }
}
