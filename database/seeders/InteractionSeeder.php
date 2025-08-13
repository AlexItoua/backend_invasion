<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interactions = [
            // Interactions avec Jean Kimbangu (ame_id: 1)
            [
                'ame_id' => 1,
                'user_id' => 2, // Pasteur Nkounkou
                'type' => 'visite',
                'note' => 'Visite à domicile. Jean montre un vif intérêt pour l\'étude biblique.',
                'date_interaction' => Carbon::create(2024, 1, 22),
            ],
            [
                'ame_id' => 1,
                'user_id' => 4, // Soeur Loubaki
                'type' => 'etude_biblique',
                'note' => 'Étude de Jean 3:16. Bonne participation.',
                'date_interaction' => Carbon::create(2024, 1, 28),
            ],

            // Interactions avec Marcelline Nkounkou (ame_id: 3)
            [
                'ame_id' => 3,
                'user_id' => 2, // Pasteur Nkounkou
                'type' => 'appel',
                'note' => 'Appel pour confirmer la visite de demain. Très réceptive.',
                'date_interaction' => Carbon::create(2024, 3, 4),
            ],
            [
                'ame_id' => 3,
                'user_id' => 5, // David Matsiona
                'type' => 'priere',
                'note' => 'Séance de prière pour des problèmes familiaux.',
                'date_interaction' => Carbon::create(2024, 3, 10),
            ],

            // Interactions avec Grâce Okombi (ame_id: 5)
            [
                'ame_id' => 5,
                'user_id' => 6, // Sarah Bouanga
                'type' => 'visite',
                'note' => 'Première visite. Accueil chaleureux. Intéressée par une cellule de prière.',
                'date_interaction' => Carbon::create(2024, 4, 12),
            ],
            [
                'ame_id' => 5,
                'user_id' => 7, // Pasteur Itoua
                'type' => 'etude_biblique',
                'note' => 'Étude sur la vie de David. Beaucoup de questions pertinentes.',
                'date_interaction' => Carbon::create(2024, 4, 18),
            ],

            // Interaction avec Didier Mboungou (ame_id: 4)
            [
                'ame_id' => 4,
                'user_id' => 3, // Frère Mbemba
                'type' => 'appel',
                'note' => 'Rappel pour l\'inviter à la réunion de jeunesse.',
                'date_interaction' => Carbon::create(2024, 3, 8),
            ],

            // Interaction avec Jonathan Itoua (ame_id: 6)
            [
                'ame_id' => 6,
                'user_id' => 7, // Pasteur Itoua
                'type' => 'priere',
                'note' => 'Prière de délivrance. Jeune homme très ému.',
                'date_interaction' => Carbon::create(2024, 5, 15),
            ],
        ];

        DB::table('interactions')->insert($interactions);
    }
}
