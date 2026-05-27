<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paneles_digitales', function (Blueprint $table) {
            $table->string('desc_costo', 255)->nullable()->default('Instalación y puesta en marcha')->after('costo_produccion');
        });

        Schema::table('paneles_ubicaciones', function (Blueprint $table) {
            $table->string('desc_costo', 255)->nullable()->default('Producción de lona e instalación')->after('costo_produccion');
        });
    }

    public function down(): void
    {
        Schema::table('paneles_digitales', function (Blueprint $table) {
            $table->dropColumn('desc_costo');
        });
        Schema::table('paneles_ubicaciones', function (Blueprint $table) {
            $table->dropColumn('desc_costo');
        });
    }
};
