<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrato_elementos', function (Blueprint $table) {
            $table->string('estado_instalacion', 30)->default('pendiente_instalacion')->after('observaciones');
            $table->date('fecha_instalacion')->nullable()->after('estado_instalacion');
            $table->date('fecha_retiro')->nullable()->after('fecha_instalacion');
        });
    }

    public function down(): void
    {
        Schema::table('contrato_elementos', function (Blueprint $table) {
            $table->dropColumn(['estado_instalacion', 'fecha_instalacion', 'fecha_retiro']);
        });
    }
};
