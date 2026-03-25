<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            
            $table->id();
            $table->unsignedBigInteger('entrenador_id')->nullable(); // OK
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('status');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            // clave foránea a la misma tabla
            $table->foreign('entrenador_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users'); // ✅ drop correcto
    }
};
