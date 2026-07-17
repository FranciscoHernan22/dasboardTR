<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Medidas corporales — una fila por mes por cliente
        Schema::create('cliente_medidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('mes'); // se guarda como día 1 del mes, ej. 2026-07-01
            $table->decimal('peso', 5, 2)->nullable();       // kg
            $table->decimal('cintura', 5, 2)->nullable();    // cm
            $table->decimal('cadera', 5, 2)->nullable();     // cm
            $table->decimal('pecho', 5, 2)->nullable();      // cm
            $table->decimal('brazo', 5, 2)->nullable();      // cm
            $table->decimal('muslo', 5, 2)->nullable();      // cm
            $table->decimal('grasa_corporal', 5, 2)->nullable(); // %
            $table->timestamps();

            $table->unique(['user_id', 'mes']);
        });

        // Fotos de progreso — varias por mes
        Schema::create('cliente_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('mes');
            $table->string('ruta');                 // path en storage
            $table->string('angulo')->nullable();    // frente / perfil / espalda (opcional)
            $table->timestamps();
        });

        // Videos de ejercicio — antes / ahora, agrupados por nombre de ejercicio
        Schema::create('cliente_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('ejercicio');
            $table->enum('tipo', ['antes', 'ahora']);
            $table->string('url'); // link (YouTube/Drive) o path si se sube archivo
            $table->timestamps();
        });

        // Notas cualitativas del entrenador
        Schema::create('cliente_notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('entrenador_id')->constrained('users')->cascadeOnDelete();
            $table->text('contenido');
            $table->string('etiqueta')->nullable(); // ej. "Energía alta", "Molestia", "Evaluación inicial"
            $table->timestamps();
        });

        // Registro de rendimiento por ejercicio (peso/series/reps por sesión)
        Schema::create('ejercicio_registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('ejercicio');
            $table->decimal('peso', 6, 2)->nullable(); // kg
            $table->unsignedInteger('series')->nullable();
            $table->unsignedInteger('reps')->nullable();
            $table->date('fecha');
            $table->timestamps();
        });

        // Sesiones de entrenamiento — para constancia/heatmap
        Schema::create('sesiones_entrenamiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->boolean('completada')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones_entrenamiento');
        Schema::dropIfExists('ejercicio_registros');
        Schema::dropIfExists('cliente_notas');
        Schema::dropIfExists('cliente_videos');
        Schema::dropIfExists('cliente_fotos');
        Schema::dropIfExists('cliente_medidas');
    }
};