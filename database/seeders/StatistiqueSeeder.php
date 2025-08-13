<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueSeeder extends Seeder
{
    public function run(): void
    {
        // Récupération des campagnes par leur nom
        $salongo = DB::table('campagnes')->where('nom', 'like', 'Opération Salongo%')->first();
        $potoPoto = DB::table('campagnes')->where('nom', 'Evangélisation Poto-Poto')->first();
        $vaccination = DB::table('campagnes')->where('nom', 'Campagne de vaccination Makélékélé')->first();

        if (!$salongo || !$potoPoto || !$vaccination) {
            dump('❌ Campagnes manquantes, vérifie les noms dans CampagneSeeder.');
            return;
        }

        $statistiques = [
            // Salongo
            [
                'campagne_id' => $salongo->id,
                'total_ames' => 42,
                'baptises' => 15,
                'fidelises' => 28,
                'nouvelles_ames' => 12,
                'date_generation' => Carbon::create(2024, 3, 25),
            ],
            [
                'campagne_id' => $salongo->id,
                'total_ames' => 56,
                'baptises' => 22,
                'fidelises' => 38,
                'nouvelles_ames' => 8,
                'date_generation' => Carbon::create(2024, 4, 30),
            ],

            // Poto-Poto
            [
                'campagne_id' => $potoPoto->id,
                'total_ames' => 78,
                'baptises' => 31,
                'fidelises' => 45,
                'nouvelles_ames' => 25,
                'date_generation' => Carbon::create(2024, 4, 15),
            ],
            [
                'campagne_id' => $potoPoto->id,
                'total_ames' => 92,
                'baptises' => 45,
                'fidelises' => 67,
                'nouvelles_ames' => 14,
                'date_generation' => Carbon::create(2024, 5, 20),
            ],

            // Makélékélé (vaccination)
            [
                'campagne_id' => $vaccination->id,
                'total_ames' => 35,
                'baptises' => 12,
                'fidelises' => 22,
                'nouvelles_ames' => 18,
                'date_generation' => Carbon::create(2024, 6, 10),
            ],
        ];

        DB::table('statistiques')->insert($statistiques);
    }
}
