<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizacion_elementos', function (Blueprint $table) {
            $table->unsignedBigInteger('servicio_id')->nullable()->after('panel_id');
            $table->foreign('servicio_id')->references('id')->on('servicios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cotizacion_elementos', function (Blueprint $table) {
            $table->dropForeign(['servicio_id']);
            $table->dropColumn('servicio_id');
        });
    }
};
