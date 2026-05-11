<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carpetas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->unsignedBigInteger('carpeta_padre_id')->nullable();
            $table->timestamps();

            $table->foreign('carpeta_padre_id')->references('id')->on('carpetas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carpetas');
    }
};
