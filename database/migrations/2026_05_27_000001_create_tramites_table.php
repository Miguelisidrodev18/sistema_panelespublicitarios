<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramites', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->nullable();
            $table->string('tipo', 200)->nullable();
            $table->string('entidad_nombre', 200)->nullable();
            $table->string('entidad_expediente', 200)->nullable();
            $table->string('codigo_tramite', 100)->nullable();
            $table->string('area_actual', 200)->nullable();
            $table->string('encargado', 200)->nullable();
            $table->text('doc_presentado')->nullable();
            $table->string('encargado_area', 200)->nullable();
            $table->string('contacto', 200)->nullable();
            $table->text('apunte_adicional')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('estado', 30)->default('en_tramite');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramites');
    }
};
