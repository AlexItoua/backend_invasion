<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Méthode optimale pour vider la table
        User::query()->delete();

        // Réactiver les contraintes
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Création des utilisateurs de base
        $users = [
             [
                'nom' => 'Itoua caleb',
                'email' => 'yvescalebitoua@gmail.com',
                'password' => Hash::make('alexandre'),
                'telephone' => '068731172',
                'role' => 'admin',
                'zone_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Jean Okombi',
                'email' => 'okombi@example.com',
                'password' => Hash::make('Admin@123'),
                'telephone' => '242064512345',
                'role' => 'admin',
                'zone_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Pasteur Nkounkou',
                'email' => 'nkounkou@example.com',
                'password' => Hash::make('Encadreur@123'),
                'telephone' => '242065523456',
                'role' => 'encadreur',
                'zone_id' => 1, // Bacongo
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Frère Mbemba',
                'email' => 'mbemba@example.com',
                'password' => Hash::make('Encadreur@123'),
                'telephone' => '242066534567',
                'role' => 'encadreur',
                'zone_id' => 2, // Poto-Poto
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Soeur Loubaki',
                'email' => 'loubaki@example.com',
                'password' => Hash::make('Evangeliste@123'),
                'telephone' => '242067545678',
                'role' => 'evangeliste',
                'zone_id' => 1, // Bacongo
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'David Matsiona',
                'email' => 'matsiona@example.com',
                'password' => Hash::make('Evangeliste@123'),
                'telephone' => '242068556789',
                'role' => 'evangeliste',
                'zone_id' => 2, // Poto-Poto
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Sarah Bouanga',
                'email' => 'bouanga@example.com',
                'password' => Hash::make('Evangeliste@123'),
                'telephone' => '242069567890',
                'role' => 'evangeliste',
                'zone_id' => 3, // Talangaï
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Pasteur Itoua',
                'email' => 'itoua@example.com',
                'password' => Hash::make('Encadreur@123'),
                'telephone' => '242060578901',
                'role' => 'encadreur',
                'zone_id' => 3, // Talangaï
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insertion avec gestion des doublons
        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        // Optionnel: Création d'utilisateurs supplémentaires sans factory
        $this->createAdditionalUsers(10);
    }

    /**
     * Crée des utilisateurs supplémentaires
     */
    protected function createAdditionalUsers(int $count): void
    {
        $roles = ['evangeliste', 'encadreur'];
        $zones = [1, 2, 3];

        for ($i = 0; $i < $count; $i++) {
            $firstName = ['Jean', 'Marie', 'Pierre', 'Paul', 'Jacques', 'Lucie', 'Ange', 'David'][array_rand(['Jean', 'Marie', 'Pierre', 'Paul', 'Jacques', 'Lucie', 'Ange', 'David'])];
            $lastName = ['Okombi', 'Nkounkou', 'Mbemba', 'Loubaki', 'Matsiona', 'Bouanga', 'Itoua', 'Kimbangu'][array_rand(['Okombi', 'Nkounkou', 'Mbemba', 'Loubaki', 'Matsiona', 'Bouanga', 'Itoua', 'Kimbangu'])];

            User::create([
                'nom' => $firstName . ' ' . $lastName,
                'email' => Str::lower($firstName) . '.' . Str::lower($lastName) . ($i + 1) . '@example.com',
                'password' => Hash::make('Password' . ($i + 1)),
                'telephone' => '24206' . rand(1000000, 9999999),
                'role' => $roles[array_rand($roles)],
                'zone_id' => $zones[array_rand($zones)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
