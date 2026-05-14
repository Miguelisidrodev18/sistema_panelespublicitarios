<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar RUC a la tabla de empresas
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('ruc', 11)->nullable()->after('nombre');
        });

        // Agregar RUC y FK a empresa en control_publicitario
        Schema::table('control_publicitario', function (Blueprint $table) {
            $table->string('ruc', 11)->nullable()->after('empresa_nombre');
            $table->foreignId('empresa_id')->nullable()->after('ruc')
                  ->constrained('empresas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('control_publicitario', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropColumn(['ruc', 'empresa_id']);
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('ruc');
        });
    }
};
