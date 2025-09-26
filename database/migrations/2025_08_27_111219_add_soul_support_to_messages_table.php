<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Rendre sender_id nullable car les âmes enverront aussi des messages
            $table->unsignedBigInteger('sender_id')->nullable()->change();

            // Ajouter sender_type pour distinguer user/ame
            $table->enum('sender_type', ['user', 'soul'])->default('user');

            // Ajouter une clé étrangère pour les âmes
            $table->foreignId('soul_id')->nullable()->constrained('ames')->onDelete('cascade');

            // Ajouter des index
            $table->index(['sender_type', 'sender_id']);
            $table->index(['sender_type', 'soul_id']);
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['soul_id']);
            $table->dropColumn('soul_id');
            $table->dropColumn('sender_type');
            $table->unsignedBigInteger('sender_id')->nullable(false)->change();
        });
    }
};
