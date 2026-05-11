<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('empresa_paneles_digitales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('panel_id');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('panel_id')->references('id')->on('paneles_digitales')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_paneles_digitales');
    }
};
