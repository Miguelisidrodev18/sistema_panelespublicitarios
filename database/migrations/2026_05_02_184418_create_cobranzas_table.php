<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cobranzas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->integer('numero_cuota')->default(0);
            $table->decimal('monto', 10, 2);
            $table->date('fecha_vencimiento');
            $table->string('estado', 20)->default('pendiente');
            $table->string('concepto', 200)->nullable();
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->index('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobranzas');
    }
};
