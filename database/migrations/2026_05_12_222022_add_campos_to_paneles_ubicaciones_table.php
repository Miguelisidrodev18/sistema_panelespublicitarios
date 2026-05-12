<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paneles_ubicaciones', function (Blueprint $table) {
            $table->string('medidas', 100)->nullable()->after('caras');
            $table->decimal('costo_produccion', 10, 2)->nullable()->after('medidas');
            $table->string('gramaje_lonas', 50)->nullable()->after('costo_produccion');
        });
    }

    public function down(): void
    {
        Schema::table('paneles_ubicaciones', function (Blueprint $table) {
            $table->dropColumn(['medidas', 'costo_produccion', 'gramaje_lonas']);
        });
    }
};
