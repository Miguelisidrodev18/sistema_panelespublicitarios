<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramite_procesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramite_id')->constrained('tramites')->cascadeOnDelete();
            $table->string('area', 200)->nullable();
            $table->string('numero_notificacion', 200)->nullable();
            $table->text('observacion')->nullable();
            $table->string('estado', 30)->default('pendiente');
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramite_procesos');
    }
};
