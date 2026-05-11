<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('control_publicitario', function (Blueprint $table) {
            $table->id();
            $table->string('empresa_nombre', 255);
            $table->string('panel_codigo', 50);
            $table->enum('tipo_panel', ['digital', 'tradicional'])->default('digital');
            $table->enum('estado', ['activo', 'pausado', 'cancelado']);
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->unique(['empresa_nombre', 'panel_codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_publicitario');
    }
};
