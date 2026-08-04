<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Valor vigente: uno por (user_id, ejercicio_id). Es lo que se
        // consulta para sugerir pesos al armar/mostrar una rutina.
        if (!Schema::hasTable('estimaciones_1rm')) {
            Schema::create('estimaciones_1rm', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('ejercicio_id')->constrained('ejercicios')->cascadeOnDelete();

                $table->decimal('valor_1rm_kg', 7, 2); // siempre en kg (unidad canónica)
                $table->char('nivel_confianza', 1);    // 'A' (1-6 reps) | 'B' (7-12) | 'C' (13-20)

                $table->integer('reps_base');           // con cuántas reps se calculó
                $table->decimal('peso_base', 7, 2);      // con qué peso se calculó (en la unidad original)
                $table->string('unidad_base', 3);        // 'kg' | 'lb' — unidad en la que el cliente registró ese dato

                $table->timestamp('fecha_calculo')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'ejercicio_id']);
            });
        }

        // Historial: TODOS los candidatos calculados, hayan reemplazado
        // o no al valor vigente. Sirve para analítica / auditoría y para
        // futuras gráficas de progreso.
        if (!Schema::hasTable('estimaciones_1rm_historial')) {
            Schema::create('estimaciones_1rm_historial', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('ejercicio_id')->constrained('ejercicios')->cascadeOnDelete();

                $table->decimal('valor_1rm_kg', 7, 2);
                $table->char('nivel_confianza', 1);

                $table->integer('reps_base');
                $table->decimal('peso_base', 7, 2);
                $table->string('unidad_base', 3);

                $table->boolean('se_uso_como_vigente')->default(false);

                $table->timestamp('fecha_calculo')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'ejercicio_id', 'fecha_calculo'], 'idx_1rm_hist_user_ej_fecha');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('estimaciones_1rm_historial');
        Schema::dropIfExists('estimaciones_1rm');
    }
};