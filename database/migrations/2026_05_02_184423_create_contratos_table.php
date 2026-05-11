<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_contrato', 50)->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->string('contratante', 200)->nullable();
            $table->string('doc_tipo', 20)->nullable();
            $table->string('doc_numero', 50)->nullable();
            $table->string('direccion', 300)->nullable();
            $table->string('tipo_contrato', 50)->nullable();
            $table->decimal('monto_total', 10, 2)->default(0);
            $table->decimal('adelanto', 10, 2)->default(0);
            $table->decimal('saldo_pendiente', 10, 2)->default(0);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('estado', 20)->default('activo');
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas')->nullOnDelete();
            $table->index('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
