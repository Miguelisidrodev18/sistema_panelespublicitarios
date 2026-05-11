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
        Schema::table('control_publicitario', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('tipo_panel');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('control_publicitario', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_fin']);
        });
    }
};
