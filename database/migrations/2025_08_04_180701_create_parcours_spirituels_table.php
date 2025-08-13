<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
Schema::create('parcours_spirituels', function (Blueprint $table) {
    $table->id();
    $table->string('nom')->unique();
    $table->text('description')->nullable();
    $table->integer('ordre')->default(1);
    $table->boolean('est_actif')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
    }

    public function down(): void {
        Schema::dropIfExists('parcours_spirituels');
    }
};
