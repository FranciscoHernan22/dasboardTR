<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutinas', function (Blueprint $table) {
            $table->id();

            // Relación con users
            $table->unsignedBigInteger('user_id');

            // Campos del ejercicio
            $table->string('tipo');
                        $table->string('grupo');
            $table->string('segmento');
            $table->string('nombre');
            $table->integer('series');
            $table->integer('reps');

            // Nuevo: entrenamiento, semana, dia
            $table->string('entrenamiento')->nullable();
            $table->integer('semana')->nullable();
            $table->integer('dia')->nullable();

            $table->timestamps();

            // Foreing key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutinas');
    }
};
