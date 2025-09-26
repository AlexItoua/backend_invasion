<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('email')->unique();
            $table->string('password'); // Ajout du champ password
            $table->string('telephone')->nullable();
            $table->enum('role', ['evangeliste', 'encadreur', 'admin', 'gagneur']);
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            $table->rememberToken(); // Important pour l'authentification
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
