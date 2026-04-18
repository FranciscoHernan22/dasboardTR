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
        $table->unsignedBigInteger('user_id');
        $table->string('tipo');
        $table->string('grupo');
        $table->string('segmento');
        $table->string('nombre');
        $table->unsignedBigInteger('ejercicio_id')->nullable();
        $table->json('series')->nullable();        // 👈 sin ->change()
        $table->unsignedInteger('orden')->default(0); // 👈 agregar
        $table->string('entrenamiento')->nullable();
        $table->integer('semana')->nullable();
        $table->integer('dia')->nullable();
        $table->timestamps();
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::dropIfExists('rutinas');  // 👈 simplificar
}
};
