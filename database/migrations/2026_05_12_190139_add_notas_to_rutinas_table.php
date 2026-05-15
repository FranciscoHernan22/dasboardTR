<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('rutinas', function (Blueprint $table) {
        $table->text('nota_sesion')->nullable()->after('descanso_unidad');
        $table->text('nota_ej')->nullable()->after('nota_sesion');
    });
}

public function down(): void
{
    Schema::table('rutinas', function (Blueprint $table) {
        $table->dropColumn(['nota_sesion', 'nota_ej']);
    });
}
};
