<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entrenadores', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('role');
            $table->date('ultimo_pago')->nullable()->after('activo');
            $table->date('vence_el')->nullable()->after('ultimo_pago');
            $table->text('notas_pago')->nullable()->after('vence_el');
        });
    }

    public function down(): void
    {
        Schema::table('entrenadores', function (Blueprint $table) {
            $table->dropColumn(['activo', 'ultimo_pago', 'vence_el', 'notas_pago']);
        });
    }
};