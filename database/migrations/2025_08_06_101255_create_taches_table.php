<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->foreignId('user_id')->constrained(); // Assignée à
            $table->foreignId('ame_id')->nullable()->constrained(); // Liée à une âme
            $table->dateTime('echeance');
            $table->enum('statut', ['en_attente', 'terminee', 'annulee']);
            $table->enum('priorite', ['basse', 'normale', 'haute']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};
