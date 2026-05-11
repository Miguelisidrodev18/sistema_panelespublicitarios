<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deudas', function (Blueprint $table) {
            $table->id();
            $table->string('acreedor', 200);
            $table->string('concepto', 300);
            $table->decimal('monto', 10, 2);
            $table->decimal('monto_pendiente', 10, 2);
            $table->date('fecha_deuda')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('prioridad', 20)->default('media');
            $table->text('notas')->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deudas');
    }
};
