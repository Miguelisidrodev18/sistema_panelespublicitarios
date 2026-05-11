<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cotizacion_elementos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotizacion_id');
            $table->string('tipo_elemento', 50)->nullable();
            $table->unsignedBigInteger('panel_id')->nullable();
            $table->string('codigo', 100)->nullable();
            $table->integer('tiempo_contrato')->nullable();
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('cotizacion_id')->references('id')->on('cotizaciones')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_elementos');
    }
};
