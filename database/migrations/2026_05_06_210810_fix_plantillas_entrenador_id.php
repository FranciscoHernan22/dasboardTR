<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plantillas', function (Blueprint $table) {
            // Quitar la foreign key actual que apunta a users
            $table->dropForeign(['entrenador_id']);

            // Agregar la correcta que apunta a entrenadores
            $table->foreign('entrenador_id')
                  ->references('id')->on('entrenadores')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('plantillas', function (Blueprint $table) {
            $table->dropForeign(['entrenador_id']);
            $table->foreign('entrenador_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }
};