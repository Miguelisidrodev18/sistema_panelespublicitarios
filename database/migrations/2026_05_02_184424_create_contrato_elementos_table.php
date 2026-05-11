<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contrato_elementos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contrato_id');
            $table->string('tipo_elemento', 50)->nullable();
            $table->unsignedBigInteger('panel_id')->nullable();
            $table->string('codigo', 100)->nullable();
            $table->integer('tiempo_contrato')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_elementos');
    }
};
