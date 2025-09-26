<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('etape_validees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcours_ame_id')->constrained()->onDelete('cascade');
            // Correction: spécifier explicitement le nom de la table référencée
            $table->foreignId('etape_parcours_id')->constrained('etape_parcours')->onDelete('cascade');
            $table->dateTime('date_validation');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['parcours_ame_id', 'etape_parcours_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etape_validees');
    }
};
