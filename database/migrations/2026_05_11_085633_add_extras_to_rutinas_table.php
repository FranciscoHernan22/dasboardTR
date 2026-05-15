// php artisan make:migration add_extras_to_rutinas_table
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rutinas', function (Blueprint $table) {
            $table->string('descanso_valor',  10)->nullable()->after('series');
            $table->string('descanso_unidad', 10)->nullable()->default('seg')->after('descanso_valor');
        });
    }

    public function down(): void
    {
        Schema::table('rutinas', function (Blueprint $table) {
            $table->dropColumn(['descanso_valor', 'descanso_unidad']);
        });
    }
};