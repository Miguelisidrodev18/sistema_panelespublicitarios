<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('control_publicitario_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('control_publicitario_id')->constrained('control_publicitario')->cascadeOnDelete();
            $table->string('estado_anterior', 20)->nullable();
            $table->string('estado_nuevo', 20);
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_publicitario_historial');
    }
};
