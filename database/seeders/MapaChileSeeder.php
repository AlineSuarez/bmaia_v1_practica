<?php

namespace Database\Seeders;

use App\Models\RegionMapa;
use App\Models\CiudadMapa;
use Illuminate\Database\Seeder;

class MapaChileSeeder extends Seeder
{
    public function run(): void
    {
        // NOTA: svg_path es DEMO (rectángulos/bloques). Sustituye por paths reales cuando quieras.
        $rm = RegionMapa::create([
            'nombre' => 'Región Metropolitana de Santiago',
            'slug'   => 'rm',
            'svg_path' => 'M 370 220 L 410 220 L 410 330 L 370 330 Z',
            'centro_lat' => -33.4489,
            'centro_lon' => -70.6693,
            'descripcion' => 'Zona demo para RM.'
        ]);

        $v = RegionMapa::create([
            'nombre' => 'Región de Valparaíso',
            'slug'   => 'valparaiso',
            'svg_path' => 'M 320 180 L 360 180 L 360 300 L 320 300 Z',
            'centro_lat' => -33.0472,
            'centro_lon' => -71.6127,
            'descripcion' => 'Zona demo para Valparaíso.'
        ]);

        // Ciudades de ejemplo
        CiudadMapa::insert([
            ['region_id'=>$rm->id,'nombre'=>'Santiago','slug'=>'santiago','lat'=>-33.45,'lon'=>-70.6667,'resumen'=>'Capital de Chile.'],
            ['region_id'=>$rm->id,'nombre'=>'Puente Alto','slug'=>'puente-alto','lat'=>-33.6167,'lon'=>-70.5667,'resumen'=>'Comuna al suroriente.'],
            ['region_id'=>$v->id,'nombre'=>'Valparaíso','slug'=>'valparaiso','lat'=>-33.0458,'lon'=>-71.6197,'resumen'=>'Puerto principal.'],
            ['region_id'=>$v->id,'nombre'=>'Viña del Mar','slug'=>'vina-del-mar','lat'=>-33.0245,'lon'=>-71.5518,'resumen'=>'Ciudad jardín.'],
        ]);
    }
}
