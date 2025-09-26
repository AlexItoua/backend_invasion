<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('statistiques')->delete();

        $campagneId = DB::table('campagnes')->where('nom', 'Invasion 2025')->value('id');

        if (!$campagneId) {
            $campagneId = DB::table('campagnes')->insertGetId([
                'nom' => 'Invasion 2025',
                'date_debut' => Carbon::create(2025, 1, 1),
                'date_fin' => Carbon::create(2025, 12, 31),
                'zone_id' => 1,
                'description' => 'Grande campagne d\'évangélisation et de mobilisation spirituelle pour 2025.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $statistiques = [
            [
                'campagne_id' => $campagneId,
                'total_ames' => 100,
                'baptises' => 40,
                'fidelises' => 60,
                'nouvelles_ames' => 25,
                'taux_conversion' => 40.0,
                'taux_fidelisation' => 60.0,
                'date_generation' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'campagne_id' => $campagneId,
                'total_ames' => 120,
                'baptises' => 55,
                'fidelises' => 80,
                'nouvelles_ames' => 30,
                'taux_conversion' => 45.8,
                'taux_fidelisation' => 66.7,
                'date_generation' => Carbon::create(2024, 6, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('statistiques')->insert($statistiques);
    }
}
