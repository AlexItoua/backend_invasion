<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Vider la table user
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        User::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Insérer seulement Itoua Caleb
        User::create([
            'nom' => 'Itoua Caleb',
            'email' => 'yvescalebitoua@gmail.com',
            'password' => Hash::make('alexandre'),
            'telephone' => '068731172',
            'role' => 'admin',
            'zone_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'nom' => 'ilitch',
            'email' => 'ilitchoint@gmail.com',
            'password' => Hash::make('invasion2025'),
            'telephone' => '068731172',
            'role' => 'admin',
            'zone_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
