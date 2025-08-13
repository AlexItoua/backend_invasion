<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ame_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['appel', 'visite', 'priere', 'etude_biblique']);
            $table->text('note')->nullable();
            $table->date('date_interaction');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('interactions');
    }
};
