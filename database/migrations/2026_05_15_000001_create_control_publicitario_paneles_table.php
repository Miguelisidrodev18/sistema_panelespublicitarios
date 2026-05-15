<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('control_publicitario_paneles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('control_publicitario_id')
                  ->constrained('control_publicitario')
                  ->onDelete('cascade');
            $table->string('panel_codigo', 50);
            $table->enum('tipo_panel', ['digital', 'tradicional'])->default('digital');
            $table->timestamps();
        });

        // Migrar datos existentes al pivot
        DB::table('control_publicitario')
            ->whereNotNull('panel_codigo')
            ->get()
            ->each(function ($reg) {
                DB::table('control_publicitario_paneles')->insert([
                    'control_publicitario_id' => $reg->id,
                    'panel_codigo'            => $reg->panel_codigo,
                    'tipo_panel'              => $reg->tipo_panel,
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]);
            });

        // Eliminar unique constraint y hacer columnas nullable
        Schema::table('control_publicitario', function (Blueprint $table) {
            $table->dropUnique(['empresa_nombre', 'panel_codigo']);
            $table->string('panel_codigo', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_publicitario_paneles');

        Schema::table('control_publicitario', function (Blueprint $table) {
            $table->string('panel_codigo', 50)->nullable(false)->change();
            $table->unique(['empresa_nombre', 'panel_codigo']);
        });
    }
};
