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
            // 1. Usuarios
            // =========================
            // IMPORTANTE:
            // Comentamos UsersTableSeeder para no volver a insertar
            // test@example.com y evitar el error de "duplicate entry".
            // Si alguna vez necesitas poblar usuarios desde cero,
            // puedes descomentar esta línea en una base de datos vacía.
            // UsersTableSeeder::class,

            // =========================
            // 2. Regiones y comunas (incluye coordenadas y UTM)
            // =========================
            RegionesComunasSeeder::class,
            ComunasCoordenadasSeeder::class,
            ComunasUtmSeeder::class,

            // =========================
            // 3. Tareas
            // =========================
            TareasGeneralesSeeder::class,
            TareasPredefinidasSeeder::class,
            AddTareasPredefinidasSeeder::class,

            // =========================
            // 4. Catálogo de flora (antiguo sistema)
            // =========================
            FenofaseSeeder::class,            // Botón, Inicio, Plena, Terminal
            FloresSeeder::class,              // Especies (Tebo, Quillay, etc.)

            // ---- Perfiles informativos para Catálogo de Flora ----
            FlorPerfilesSeeder::class,        // descripciones, hábitat, usos, imagen de portada, etc.

            // ==== Duraciones por fenofase (offsets desde "botón") ====
            FlorFaseDuracionesSeeder::class,

            // Imágenes por especie y fenofase
            FlorImagenesSeeder::class,

            // ==== Mapa de Chile (regiones y ciudades para el explorador local) ====
            MapaChileSeeder::class,

            // ==== Explorador de Zonas: tablas region_maps + comuna_maps ====
            RegionMapsSeeder::class,

            // =========================
            // 5. Perfiles por Región y por Comuna
            // =========================
            FixRegionesBiobioSeeder::class,
            FixComunasBiobioSeeder::class,
            RegionPerfilSeeder::class,
            ComunaPerfilSeeder::class,

            // =========================
            // 6. NUEVO: Catálogo de flora (nueva tabla flora_species)
            // =========================
            FloraSpeciesSeeder::class,
        ]);

        // Alternativamente (misma ejecución), podrías llamar explícitamente:
        // $this->call(\Database\Seeders\MapaChileSeeder::class);
    }
}
