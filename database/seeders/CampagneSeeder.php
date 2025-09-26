<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CampagneSeeder extends Seeder
{
    public function run(): void
    {
        $campagnes = [
            [
                'nom' => 'Invasion 2025',
                'date_debut' => Carbon::create(2025, 1, 1),
                'date_fin' => Carbon::create(2025, 12, 31),
                'zone_id' => 1,
                'description' => 'Grande campagne d\'évangélisation et de mobilisation spirituelle pour 2025.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('campagnes')->insert($campagnes);
    }
}
