<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ames', function (Blueprint $table) {
            // 🔥 Changer de string() à mediumText() pour supporter Base64
            $table->mediumText('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ames', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });
    }
};
