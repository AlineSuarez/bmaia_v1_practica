<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Comuna;

class ComunasFaltantesSeeder extends Seeder
{
    public function run()
    {
        $faltantes = [
            'Región de Valparaíso' => [
                'Juan Fernández', 'Puchuncaví', 'Quintero', 'Limache', 'Olmué', 'Isla de Pascua'
            ],
            'Región Metropolitana de Santiago' => [
                'Pirque', 'San José de Maipo', 'Quinta Normal'
            ],
            'Región de O’Higgins' => [
                'Olivar', 'Paredones', 'Quinta de Tilcoco', 'Chépica'
            ],
            'Región del Maule' => [
                'Empedrado', 'Rauco', 'San Javier'
            ],
            'Región de Ñuble' => [
                'Coelemu'
            ],
            'Región del Biobío' => [
                'Alto Biobío'
            ],
            'Región de La Araucanía' => [
                'Curacautín', 'Lonquimay'
            ],
            'Región de Los Lagos' => [
                'Cochamó', 'Puerto Octay', 'Puyehue'
            ],
        ];

        foreach ($faltantes as $regionNombre => $comunas) {
            $region = Region::where('nombre', $regionNombre)->first();

            if (!$region) {
                $this->command->warn("⚠️ Región no encontrada: $regionNombre (debes crearla primero)");
                continue;
            }

            foreach ($comunas as $comunaNombre) {
                $existe = Comuna::where('nombre', $comunaNombre)
                    ->where('region_id', $region->id)
                    ->exists();

                if (!$existe) {
                    Comuna::create([
                        'nombre' => $comunaNombre,
                        'region_id' => $region->id,
                    ]);
                    $this->command->info("✅ Comuna agregada: $comunaNombre ({$region->nombre})");
                } else {
                    $this->command->line("✔️ Ya existe: $comunaNombre ({$region->nombre})");
                }
            }
        }
    }
}
