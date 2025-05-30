<?php

namespace Database\Seeders;

use App\Models\Apiario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApiarioSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1; // sustituye por un user_id válido en tu sistema

        // 2 Apiarios FIJOS
        Apiario::create([
            'nombre'               => 'Colmenar Central',
            'tipo_apiario'         => 'fijo',
            'user_id'              => $userId,
            'temporada_produccion' => now()->year,
            'num_colmenas'         => 5,
            'registro_sag'         => 'FJ-001',
            'activo'               => 1,
        ]);
        Apiario::create([
            'nombre'               => 'El Roble',
            'tipo_apiario'         => 'fijo',
            'user_id'              => $userId,
            'temporada_produccion' => now()->year,
            'num_colmenas'         => 8,
            'registro_sag'         => 'FJ-002',
            'activo'               => 1,
        ]);

        // 2 Apiarios TRASHUMANTES “base”
        Apiario::create([
            'nombre'               => 'Trashu 1',
            'tipo_apiario'         => 'trashumante',
            'user_id'              => $userId,
            'temporada_produccion' => now()->year,
            'num_colmenas'         => 3,
            'registro_sag'         => 'TR-001',
            'activo'               => 1,
        ]);
        Apiario::create([
            'nombre'               => 'Trashu 2',
            'tipo_apiario'         => 'trashumante',
            'user_id'              => $userId,
            'temporada_produccion' => now()->year,
            'num_colmenas'         => 4,
            'registro_sag'         => 'TR-002',
            'activo'               => 1,
        ]);
    }
}
