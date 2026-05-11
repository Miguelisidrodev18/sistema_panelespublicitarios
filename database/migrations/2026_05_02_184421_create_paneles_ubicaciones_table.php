<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paneles_ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 200);
            $table->string('direccion', 300)->nullable();
            $table->string('caras', 100)->nullable();
            $table->string('foto', 300)->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paneles_ubicaciones');
    }
};
