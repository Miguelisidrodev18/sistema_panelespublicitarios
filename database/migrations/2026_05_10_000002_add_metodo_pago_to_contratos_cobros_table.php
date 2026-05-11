<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos_cobros', function (Blueprint $table) {
            $table->string('metodo_pago', 30)->nullable()->after('tipo_cobro');
        });
    }

    public function down(): void
    {
        Schema::table('contratos_cobros', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });
    }
};
