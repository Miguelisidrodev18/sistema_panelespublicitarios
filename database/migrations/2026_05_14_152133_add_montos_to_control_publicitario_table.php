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
            $table->decimal('monto_pagado', 10, 2)->nullable()->after('notas');
            $table->decimal('monto_pendiente', 10, 2)->nullable()->after('monto_pagado');
        });
    }

    public function down(): void
    {
        Schema::table('control_publicitario', function (Blueprint $table) {
            $table->dropColumn(['monto_pagado', 'monto_pendiente']);
        });
    }
};
