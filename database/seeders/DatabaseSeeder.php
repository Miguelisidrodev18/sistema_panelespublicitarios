<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin por defecto
        Usuario::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('Admin123'),
                'nombre_completo' => 'Administrador',
                'email' => 'admin@buhooh.com',
                'rol' => 'admin',
                'activo' => true,
                'permisos' => [],
            ]
        );
    }
}
