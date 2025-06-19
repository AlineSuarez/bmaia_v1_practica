<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Usuarios
            UsersTableSeeder::class,

            // 2. Regiones y comunas (incluye coordenadas y UTM)
            RegionesComunasSeeder::class,
            ComunasCoordenadasSeeder::class,
            ComunasUtmSeeder::class,

            // 3. Tareas
            TareasGeneralesSeeder::class,
            TareasPredefinidasSeeder::class,
            AddTareasPredefinidasSeeder::class,
        ]);
    }
}
