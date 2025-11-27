<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // =========================
            // Usuarios
            // =========================
            // IMPORTANTE:
            // Comentamos UsersTableSeeder para no volver a insertar
            // test@example.com y evitar el error de "duplicate entry".
            // Si alguna vez necesitas poblar usuarios desde cero,
            // puedes descomentar esta línea en una base de datos vacía.
            UsersTableSeeder::class,
            // =========================
            //  Regiones y comunas (incluye coordenadas y UTM)
            // =========================
            RegionesComunasSeeder::class,
            ComunasCoordenadasSeeder::class,
            ComunasUtmSeeder::class,

            // =========================
            //  Tareas
            // =========================
            TareasGeneralesSeeder::class,
            TareasPredefinidasSeeder::class,
            AddTareasPredefinidasSeeder::class,

            // =========================
            //  NUEVO: Catálogo de flora (nueva tabla flora_species)
            // =========================
            FloraSpeciesSeeder::class,
        ]);

        
    }
}
