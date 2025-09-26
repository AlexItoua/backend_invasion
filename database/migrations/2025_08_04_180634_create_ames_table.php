<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ames', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('telephone');
            $table->string('device_token')->nullable();
            $table->boolean('notifications_actives')->default(true);
            $table->string('image')->nullable();
            $table->boolean('suivi')->default(false);
            $table->date('derniere_interaction')->nullable();
            $table->enum('sexe', ['homme', 'femme']);
            $table->integer('age')->nullable();
            $table->string('adresse')->nullable();
            $table->string('quartier')->nullable();
            $table->string('ville')->nullable();
            $table->date('date_conversion')->nullable();
            $table->foreignId('campagne_id')->constrained()->cascadeOnDelete();
            $table->string('type_decision')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('geoloc_accuracy', 8, 2)->nullable();
            $table->timestamp('geoloc_timestamp')->nullable();
            $table->foreignId('assigne_a')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cellule_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index('cellule_id');
            $table->index('assigne_a');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ames');
    }
};
