<?php
// DESTINO: database/migrations/2026_06_20_180000_fix_entrenador_id_references_entrenadores.php
//
// Corrige el error de diseño: la llave foránea de ejercicios.entrenador_id
// apuntaba a `users`, pero los entrenadores en realidad viven en su propia
// tabla `entrenadores` (modelo App\Models\Entrenador). Esta migración:
// 1) Quita la llave foránea equivocada (-> users)
// 2) Pone la llave foránea correcta (-> entrenadores)
// 3) Mueve la bandera "ejercicios_default_clonados" de `users` a `entrenadores`

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ejercicios', function (Blueprint $table) {
            $table->dropForeign(['entrenador_id']);
        });

        Schema::table('ejercicios', function (Blueprint $table) {
            $table->foreign('entrenador_id')
                ->references('id')->on('entrenadores')
                ->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ejercicios_default_clonados');
        });

        Schema::table('entrenadores', function (Blueprint $table) {
            $table->boolean('ejercicios_default_clonados')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('ejercicios', function (Blueprint $table) {
            $table->dropForeign(['entrenador_id']);
        });

        Schema::table('ejercicios', function (Blueprint $table) {
            $table->foreign('entrenador_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });

        Schema::table('entrenadores', function (Blueprint $table) {
            $table->dropColumn('ejercicios_default_clonados');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('ejercicios_default_clonados')->default(false);
        });
    }
};