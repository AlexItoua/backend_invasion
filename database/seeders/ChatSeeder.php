<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Ame;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::first();
        $user2 = User::skip(1)->first();
        $ame   = Ame::inRandomOrder()->first(); // 👈 récupérer une âme au hasard

        if (!$user1 || !$user2 || !$ame) {
            return; // pas assez de données pour créer une conversation
        }

        // Créer une conversation liée à une âme
        $conversation = Conversation::create([
            'titre' => 'Test Chat',
            'ame_id' => $ame->id, // 👈 important !
        ]);

        // Ajouter les participants
        $conversation->participants()->attach([$user1->id, $user2->id]);

        // Créer des messages
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user1->id,
            'contenu' => 'Salut 👋',
            'date_envoi' => now(),
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user2->id,
            'contenu' => 'Hello !',
            'date_envoi' => now(),
        ]);
    }
}
