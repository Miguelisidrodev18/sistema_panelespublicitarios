<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pagos_deudas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deuda_id');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->string('metodo_pago', 50)->nullable();
            $table->text('notas')->nullable();
            $table->string('comprobante', 300)->nullable();
            $table->timestamps();

            $table->foreign('deuda_id')->references('id')->on('deudas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_deudas');
    }
};
