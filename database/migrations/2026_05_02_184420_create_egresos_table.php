<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('egresos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->string('tipo', 50)->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('concepto', 300)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('comprobante', 300)->nullable();
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas')->nullOnDelete();
            $table->index('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egresos');
    }
};
