<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contratos_cobros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contrato_id');
            $table->string('tipo_cobro', 50)->nullable();
            $table->decimal('monto', 10, 2);
            $table->date('fecha_cobro');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos_cobros');
    }
};
