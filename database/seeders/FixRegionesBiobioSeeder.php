<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class FixRegionesBiobioSeeder extends Seeder
{
    public function run(): void
    {
        // Crea la región Biobío si no existe.
        // Usamos solo columnas seguras que sabemos que tienes: nombre y abreviatura.
        Region::firstOrCreate(
            ['nombre' => 'Biobío'],
            ['abreviatura' => 'BIOBIO']
        );
    }
}
