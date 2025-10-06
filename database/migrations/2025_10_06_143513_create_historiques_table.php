<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('historiques', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // celui qui fait l’action
            $table->string('action'); // ex: suppression
            $table->string('description')->nullable(); // détails
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historiques');
    }
};