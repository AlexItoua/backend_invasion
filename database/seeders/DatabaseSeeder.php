<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Cellule;
use App\Models\Interaction;
use App\Models\ParcoursSpirituel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
public function run()
{
    $this->call([
        ZoneSeeder::class,
        UserSeeder::class,
        CampagneSeeder::class,
        CelluleSeeder::class,
        AmeSeeder::class,
        ParcoursSpirituelSeeder::class,
        InteractionSeeder::class,
        EtapeValideeSeeder::class,
        NotificationSeeder::class,
        StatistiqueSeeder::class,
        RoleSeeder::class,
    ]);
}

}
