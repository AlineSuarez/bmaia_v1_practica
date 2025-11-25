<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Comuna;

class FixComunasBiobioSeeder extends Seeder
{
    public function run(): void
    {
        $region = Region::where('nombre', 'Biobío')
            ->orWhere('abreviatura', 'BIOBIO')
            ->first();

        if (!$region) {
            throw new \RuntimeException("No se encontró la región Biobío; corre primero FixRegionesBiobioSeeder.");
        }

        $comunas = ['Concepción', 'Los Ángeles', 'Coronel'];

        foreach ($comunas as $nombre) {
            Comuna::firstOrCreate(
                ['nombre' => $nombre, 'region_id' => $region->id],
                [] // sin más columnas para evitar choques con tu esquema
            );
        }
    }
}
