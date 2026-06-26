<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('almacen_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('almacen_id')->constrained('almacenes')->cascadeOnDelete();
            $table->string('nombre', 200);
            $table->string('codigo', 50)->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('serie', 100)->nullable();
            $table->enum('tipo', ['maquina', 'herramienta', 'indumentaria', 'materiales']);
            $table->string('unidad_medida', 30)->default('unidad');
            $table->integer('anio_compra')->nullable();
            $table->decimal('stock_actual', 12, 2)->default(0);
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen_items');
    }
};
