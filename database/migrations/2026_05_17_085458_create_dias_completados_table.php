<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dias_completados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('semana');
            $table->unsignedInteger('dia');
            $table->timestamp('fecha_completado')->useCurrent();
            $table->unique(['user_id', 'semana', 'dia']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dias_completados');
    }
};