<?php
// DESTINO: database/migrations/2026_06_20_120000_add_entrenador_id_to_ejercicios_table.php
//
// OJO: si todavía tienes el archivo de migración de la vez pasada
// (2026_06_19_120000_add_entrenador_id_to_ejercicios_table.php) y solo le
// hiciste rollback (no lo borraste), NO necesitas este archivo nuevo —
// con correr "php artisan migrate" otra vez te vuelve a crear las columnas.
// Usa este archivo SOLO si ya borraste el original.

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
            $table->boolean('ejercicios_default_clonados')->default(false)->after('entrenador_id');
        });

        // Los ejercicios que ya tienes cargados quedan con entrenador_id = NULL:
        // ese es el catálogo "default" que se clona UNA SOLA VEZ a cada
        // entrenador la primera vez que entra a /entrenador/ejercicios.
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