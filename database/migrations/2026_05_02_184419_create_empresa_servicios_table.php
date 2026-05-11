<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('empresa_servicios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('servicio_id');
            $table->decimal('monto', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['empresa_id', 'servicio_id']);
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('servicio_id')->references('id')->on('servicios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_servicios');
    }
};
