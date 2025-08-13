<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ames', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('telephone')->nullable();
            $table->string('image')->nullable(); // ligne à ajouter
            $table->boolean('suivi')->default(false); // Pour le badge "Suivi"
            $table->date('derniere_interaction')->nullable(); // Pour afficher "Dernière interaction"

            $table->enum('sexe', ['H', 'F']);
            $table->integer('age')->nullable();
            $table->string('adresse')->nullable();
            $table->date('date_conversion')->nullable();
            $table->foreignId('campagne_id')->constrained()->cascadeOnDelete();
            $table->string('type_decision')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('assigne_a')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cellule_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ames');
    }
};
