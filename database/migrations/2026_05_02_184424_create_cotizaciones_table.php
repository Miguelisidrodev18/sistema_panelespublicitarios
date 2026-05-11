<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->nullable();
            $table->string('cliente_nombre', 200)->nullable();
            $table->string('cliente_empresa', 200)->nullable();
            $table->string('cliente_telefono', 50)->nullable();
            $table->string('cliente_email', 100)->nullable();
            $table->string('tipo_contrato', 50)->nullable();
            $table->decimal('monto_propuesto', 10, 2)->default(0);
            $table->date('fecha_cotizacion')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->text('notas')->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
