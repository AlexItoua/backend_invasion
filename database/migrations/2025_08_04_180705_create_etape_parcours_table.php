<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('etape_parcours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcours_spirituel_id')->constrained()->onDelete('cascade');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->text('contenu')->nullable();
            $table->integer('ordre')->default(1);
            $table->integer('duree_estimee_minutes')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etape_parcours');
    }
};
