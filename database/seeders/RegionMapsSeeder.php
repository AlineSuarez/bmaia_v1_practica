<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegionMaps;
use App\Models\ComunaMaps;

class RegionMapsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==============================
        // EJEMPLOS PARA PROBAR EL MAPA
        // ==============================
        // OJO: iso_code debe coincidir con el id del <path> en tu chile.svg
        // (por lo que se ve en tu pantalla: CL-AN, CL-AT, CL-VS, etc.)

        // ---------- Región de Antofagasta ----------
        $antofa = RegionMaps::create([
            'nombre'   => 'Antofagasta',
            'iso_code' => 'CL-AN',          // id del path en el SVG
            'slug_svg' => 'antofagasta',    // opcional
        ]);

        ComunaMaps::insert([
            ['region_maps_id' => $antofa->id, 'nombre' => 'Antofagasta'],
            ['region_maps_id' => $antofa->id, 'nombre' => 'Mejillones'],
            ['region_maps_id' => $antofa->id, 'nombre' => 'Sierra Gorda'],
            ['region_maps_id' => $antofa->id, 'nombre' => 'Taltal'],
        ]);

        // ---------- Región de Atacama ----------
        $atacama = RegionMaps::create([
            'nombre'   => 'Atacama',
            'iso_code' => 'CL-AT',
            'slug_svg' => 'atacama',
        ]);

        ComunaMaps::insert([
            ['region_maps_id' => $atacama->id, 'nombre' => 'Copiapó'],
            ['region_maps_id' => $atacama->id, 'nombre' => 'Caldera'],
            ['region_maps_id' => $atacama->id, 'nombre' => 'Tierra Amarilla'],
        ]);

        // ---------- Región de Valparaíso (ejemplo, porque la probaste en el mapa) ----------
        $valpo = RegionMaps::create([
            'nombre'   => 'Valparaíso',
            'iso_code' => 'CL-VS',
            'slug_svg' => 'valparaiso',
        ]);

        ComunaMaps::insert([
            ['region_maps_id' => $valpo->id, 'nombre' => 'Valparaíso'],
            ['region_maps_id' => $valpo->id, 'nombre' => 'Viña del Mar'],
            ['region_maps_id' => $valpo->id, 'nombre' => 'Quilpué'],
            ['region_maps_id' => $valpo->id, 'nombre' => 'Villa Alemana'],
        ]);

        // Luego puedes seguir agregando más regiones/comunas aquí
        // o crear más seeders específicos si quieres.
    }
}
