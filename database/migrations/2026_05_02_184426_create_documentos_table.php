<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->string('nombre', 200);
            $table->string('tipo', 50)->nullable();
            $table->string('archivo', 300)->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('tamanio')->nullable();
            $table->unsignedBigInteger('subido_por')->nullable();
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
