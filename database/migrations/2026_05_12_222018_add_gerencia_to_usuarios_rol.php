<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('admin','gerencia','empresa') NOT NULL DEFAULT 'empresa'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('admin','empresa') NOT NULL DEFAULT 'empresa'");
    }
};
