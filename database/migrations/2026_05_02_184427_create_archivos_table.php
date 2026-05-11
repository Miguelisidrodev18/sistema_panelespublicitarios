<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carpeta_id');
            $table->string('nombre_original', 300);
            $table->string('nombre_archivo', 300);
            $table->string('tipo_archivo', 100)->nullable();
            $table->integer('tamano')->nullable();
            $table->timestamps();

            $table->foreign('carpeta_id')->references('id')->on('carpetas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
};
