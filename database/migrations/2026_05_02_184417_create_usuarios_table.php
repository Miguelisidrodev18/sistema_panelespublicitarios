<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('email', 100)->nullable();
            $table->string('nombre_completo', 100)->nullable();
            $table->enum('rol', ['admin', 'empresa'])->default('empresa');
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->boolean('activo')->default(true);
            $table->json('permisos')->nullable();
            $table->timestamp('ultimo_acceso')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
