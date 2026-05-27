<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paneles_digitales', function (Blueprint $table) {
            $table->decimal('costo_produccion', 10, 2)->nullable()->after('tandas');
        });
    }

    public function down(): void
    {
        Schema::table('paneles_digitales', function (Blueprint $table) {
            $table->dropColumn('costo_produccion');
        });
    }
};
