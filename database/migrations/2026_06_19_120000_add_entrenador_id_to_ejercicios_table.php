<?php
// DESTINO: database/migrations/2026_06_19_120000_add_entrenador_id_to_ejercicios_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ejercicios', function (Blueprint $table) {
            $table->foreignId('entrenador_id')->nullable()->after('id')
                ->constrained('users')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            // Bandera para saber si a este entrenador ya se le clonó
            // el catálogo "default" de ejercicios. Se usa para que el
            // clonado ocurra UNA SOLA VEZ, aunque después borre todos
            // sus ejercicios (no se le vuelven a regenerar solos).
            $table->boolean('ejercicios_default_clonados')->default(false)->after('entrenador_id');
        });

        // IMPORTANTE: los ejercicios que ya existían en tu tabla (los que
        // metiste a mano por MySQL) se quedan con entrenador_id = NULL.
        // Esos son ahora el catálogo "default": cada entrenador nuevo
        // (o que entra por primera vez a /entrenador/ejercicios) recibe
        // una copia propia y editable de ese catálogo, sin tocar el original.
    }

    public function down(): void
    {
        Schema::table('ejercicios', function (Blueprint $table) {
            $table->dropForeign(['entrenador_id']);
            $table->dropColumn('entrenador_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ejercicios_default_clonados');
        });
    }
};