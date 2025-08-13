<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('etapes_validees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ame_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parcours_spirituel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date_validation')->nullable();
            $table->text('commentaires')->nullable();
            $table->timestamps();

            $table->unique(['ame_id', 'parcours_spirituel_id']); // Une seule validation par âme et parcours
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapes_validees');
    }
};
