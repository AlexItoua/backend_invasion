<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('statistiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campagne_id')->constrained()->cascadeOnDelete();
            $table->integer('total_ames')->default(0);
            $table->integer('baptises')->default(0);
            $table->integer('fidelises')->default(0);
            $table->integer('nouvelles_ames')->default(0);
            $table->float('taux_conversion')->nullable();   // <---
            $table->float('taux_fidelisation')->nullable(); // <---
            $table->date('date_generation');
            $table->timestamps();

            $table->unique(['campagne_id', 'date_generation']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistiques');
    }
};
