<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('almacen_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('almacen_item_id')->constrained('almacen_items')->cascadeOnDelete();
            $table->enum('tipo_movimiento', ['entrada', 'salida']);
            $table->decimal('cantidad', 12, 2);
            $table->decimal('saldo', 12, 2);
            $table->date('fecha');
            $table->string('detalle', 300)->nullable();
            $table->foreignId('responsable_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('panel_digital_id')->nullable()->constrained('paneles_digitales')->nullOnDelete();
            $table->foreignId('panel_ubicacion_id')->nullable()->constrained('paneles_ubicaciones')->nullOnDelete();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->foreignId('registrado_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen_movimientos');
    }
};
