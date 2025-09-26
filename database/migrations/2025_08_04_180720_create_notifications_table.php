<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destinataire_id')
                ->nullable() // autoriser null pour notifications globales
                ->constrained('ames')
                ->onDelete('cascade');
            $table->string('type');
            $table->text('contenu');
            $table->boolean('lu')->default(false);
            $table->timestamp('date_notification')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
