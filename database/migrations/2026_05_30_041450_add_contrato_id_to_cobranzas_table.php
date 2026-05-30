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
        Schema::table('cobranzas', function (Blueprint $table) {
            $table->unsignedBigInteger('contrato_id')->nullable()->after('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::table('cobranzas', function (Blueprint $table) {
            $table->dropColumn('contrato_id');
        });
    }
};
