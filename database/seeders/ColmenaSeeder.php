<?php

namespace Database\Seeders;

use App\Models\Apiario;
use App\Models\Colmena;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ColmenaSeeder extends Seeder
{
    public function run(): void
    {
        $apiarios = Apiario::where('tipo_apiario', 'fijo')->take(2)->get();

        foreach ($apiarios as $apiario) {
            for ($i = 1; $i <= 5; $i++) {
                Colmena::create([
                    'apiario_id'     => $apiario->id,
                    'nombre'         => "Colmena {$i} del apiario {$apiario->id}",
                    'codigo_qr'      => Str::uuid(),
                    'color_etiqueta' => fake()->safeHexColor(),
                    'numero'         => (string)$i,
                    'estado_inicial' => 'saludable',
                    'numero_marcos'  => rand(6, 10), // Valor realista
                    'observaciones'  => 'Generada por Seeder',
                    'historial'      => json_encode([]),
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }
    }
}
