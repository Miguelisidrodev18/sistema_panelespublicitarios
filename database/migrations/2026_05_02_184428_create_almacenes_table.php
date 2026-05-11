<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('almacenes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('codigo', 20);
            $table->text('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('responsable', 100)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->boolean('es_principal')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacenes');
    }
};
