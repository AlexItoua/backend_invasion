<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InteractionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('interactions')->delete();

        $interactions = [
            [
                'ame_id' => 1,
                'date_interaction' => Carbon::create(2024, 1, 22),
                'note' => 'Visite à domicile. Jean montre un vif intérêt pour l\'étude biblique.',
                'type' => 'visite',
                'user_id' => 1, // Itoua Caleb
            ],
            [
                'ame_id' => 1,
                'date_interaction' => Carbon::create(2024, 1, 28),
                'note' => 'Étude de Jean 3:16. Bonne participation.',
                'type' => 'etude_biblique',
                'user_id' => 1, // remplacé 4 par 1
            ],
            [
                'ame_id' => 3,
                'date_interaction' => Carbon::create(2024, 3, 4),
                'note' => 'Appel pour confirmer la visite de demain. Très réceptive.',
                'type' => 'appel',
                'user_id' => 1,
            ],
            [
                'ame_id' => 3,
                'date_interaction' => Carbon::create(2024, 3, 10),
                'note' => 'Séance de prière pour des problèmes familiaux.',
                'type' => 'priere',
                'user_id' => 1,
            ],
            [
                'ame_id' => 5,
                'date_interaction' => Carbon::create(2024, 4, 12),
                'note' => 'Première visite. Accueil chaleureux. Intéressée par une cellule de prière.',
                'type' => 'visite',
                'user_id' => 1,
            ],
            [
                'ame_id' => 5,
                'date_interaction' => Carbon::create(2024, 4, 18),
                'note' => 'Étude sur la vie de David. Beaucoup de questions pertinentes.',
                'type' => 'etude_biblique',
                'user_id' => 1, // remplacé 7 par 1
            ],
            [
                'ame_id' => 4,
                'date_interaction' => Carbon::create(2024, 3, 8),
                'note' => 'Rappel pour l\'inviter à la réunion de jeunesse.',
                'type' => 'appel',
                'user_id' => 1,
            ],
            [
                'ame_id' => 6,
                'date_interaction' => Carbon::create(2024, 5, 15),
                'note' => 'Prière de délivrance. Jeune homme très ému.',
                'type' => 'priere',
                'user_id' => 1,
            ],
        ];

        DB::table('interactions')->insert($interactions);
    }
}
