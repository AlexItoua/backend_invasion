<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
 Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->string('titre');
    $table->text('message');
    $table->enum('type', ['sms', 'push', 'email', 'in_app']);
    $table->foreignId('destinataire_id')->nullable()->constrained('users')->nullOnDelete();
    $table->enum('statut', ['en_attente', 'envoyee', 'lue', 'echouee'])->default('en_attente');
    $table->dateTime('date_envoi')->nullable();
    $table->json('metadata')->nullable();
    $table->foreignId('ame_id')->nullable()->constrained()->nullOnDelete(); // Pour lier aux âmes
    $table->timestamps();

    $table->index(['destinataire_id', 'statut']); // Index pour les requêtes fréquentes
});
    }

    public function down(): void {
        Schema::dropIfExists('notifications');
    }
};
