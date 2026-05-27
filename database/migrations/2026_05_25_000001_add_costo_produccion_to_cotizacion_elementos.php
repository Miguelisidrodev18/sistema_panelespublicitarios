<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizacion_elementos', function (Blueprint $table) {
            $table->string('subtipo', 20)->nullable()->after('tipo_elemento');
            $table->decimal('costo_produccion', 10, 2)->nullable()->after('precio_unitario');
            $table->string('desc_costo', 200)->nullable()->after('costo_produccion');
        });
    }

    public function down(): void
    {
        Schema::table('cotizacion_elementos', function (Blueprint $table) {
            $table->dropColumn(['subtipo', 'costo_produccion', 'desc_costo']);
        });
    }
};
