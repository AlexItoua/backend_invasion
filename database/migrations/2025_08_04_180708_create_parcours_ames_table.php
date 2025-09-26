<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parcours_ames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ame_id')->constrained()->onDelete('cascade');
            $table->foreignId('parcours_spirituel_id')->constrained()->onDelete('cascade');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin')->nullable();
            $table->enum('statut', ['en_cours', 'termine', 'abandonne'])->default('en_cours');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['ame_id', 'parcours_spirituel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcours_ames');
    }
};
