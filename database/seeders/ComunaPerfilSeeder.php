<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Comuna;
use App\Models\ComunaPerfil;

class ComunaPerfilSeeder extends Seeder
{
    public function run(): void
    {
        $region = Region::where('nombre', 'Biobío')
            ->orWhere('nombre', 'BIOBIO')
            ->orWhere('abreviatura', 'BIOBIO')
            ->first();

        if (!$region) {
            throw new \RuntimeException("No se encontró la región 'Biobío' en 'regiones'. Corre primero los seeders base de regiones/comunas.");
        }

        $comunasDemo = [
            'Concepción' => [
                'bosque_nativo_ha'            => 12000,
                'plantaciones_forestales_ha'  => 8000,
                'bosque_nativo_pct'           => 55.0,
                'plantaciones_forestales_pct' => 37.0,
                'elevacion_promedio_m'        => 130,
                'precipitacion_anual_mm'      => 1200,
                'notas'                       => 'Capital regional. Alta presencia de bosque urbano y periurbano.',
            ],
            'Los Ángeles' => [
                'bosque_nativo_ha'            => 18000,
                'plantaciones_forestales_ha'  => 15000,
                'bosque_nativo_pct'           => 48.0,
                'plantaciones_forestales_pct' => 42.0,
                'elevacion_promedio_m'        => 140,
                'precipitacion_anual_mm'      => 1100,
                'notas'                       => 'Fuerte presencia de plantaciones forestales.',
            ],
            'Coronel' => [
                'bosque_nativo_ha'            => 3000,
                'plantaciones_forestales_ha'  => 6000,
                'bosque_nativo_pct'           => 30.0,
                'plantaciones_forestales_pct' => 60.0,
                'elevacion_promedio_m'        => 60,
                'precipitacion_anual_mm'      => 1000,
                'notas'                       => 'Zona costera con matriz industrial y forestal.',
            ],
        ];

        foreach ($comunasDemo as $nombreComuna => $valores) {
            $comuna = Comuna::where('nombre', $nombreComuna)
                ->where('region_id', $region->id)
                ->first();

            if (!$comuna) {
                $this->command->warn("⚠️  No se encontró la comuna '{$nombreComuna}' en la región Biobío. La omito.");
                continue;
            }

            ComunaPerfil::updateOrCreate(
                ['comuna_id' => $comuna->id],
                array_merge(['comuna_id' => $comuna->id], $valores)
            );
        }
    }
}
