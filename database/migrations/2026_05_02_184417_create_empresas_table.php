<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->boolean('panel_digital')->default(false);
            $table->boolean('panel_tradicional')->default(false);
            $table->boolean('marketing_digital')->default(false);
            $table->string('otros_servicios', 500)->nullable();
            $table->string('tipo_contrato', 50)->default('tradicional');
            $table->text('detalles_convenio')->nullable();
            $table->boolean('bonificacion')->default(false);
            $table->boolean('adendas_pagos')->default(false);
            $table->text('comentario_bonificacion')->nullable();
            $table->text('comentario_adendas')->nullable();
            $table->string('encargado', 100)->nullable();
            $table->decimal('monto', 10, 2)->default(0);
            $table->integer('dias_duracion')->default(0);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('contrato_pdf', 500)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
