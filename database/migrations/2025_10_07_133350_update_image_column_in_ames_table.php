<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ames', function (Blueprint $table) {
            // 🔥 CHANGEMENT : Passage de string() à text() pour supporter les images Base64
            $table->text('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ames', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });
    }
};
