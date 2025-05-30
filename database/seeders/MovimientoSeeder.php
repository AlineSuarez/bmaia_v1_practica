<?php

namespace Database\Seeders;

use App\Models\MovimientoColmena;
use App\Models\Colmena;
use App\Models\Apiario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MovimientoSeeder extends Seeder
{
    public function run(): void
    {
        $colmenas = Colmena::take(5)->get();
        $apiariosFijos = Apiario::where('tipo_apiario', 'fijo')->pluck('id');
        $apiarioTrash = Apiario::create([
            'nombre'               => 'Apiario Trashumante Prueba',
            'registro_sag'         => 'SAG-TEST-'.Str::random(4),
            'tipo_apiario'         => 'trashumante',
            'user_id'              => $colmenas->first()->apiario->user_id,
            'temporada_produccion' => now()->year,
            'num_colmenas'         => $colmenas->count(),
            'latitud'              => 0.0,
            'longitud'             => 0.0,
            'activo'               => 1,
         ]);

        foreach ($colmenas as $colmena) {
            MovimientoColmena::create([
                'colmena_id' => $colmena->id,
                'apiario_origen_id' => $colmena->apiario_id,
                'apiario_destino_id' => $apiarioTrash->id,
                'tipo_movimiento' => 'traslado',
                'fecha_movimiento' => now()->subDays(rand(1, 30)),
            ]);

            MovimientoColmena::create([
                'colmena_id' => $colmena->id,
                'apiario_origen_id' => $apiarioTrash->id,
                'apiario_destino_id' => $colmena->apiario_id,
                'tipo_movimiento' => 'retorno',
                'fecha_movimiento' => now()->subDays(rand(1, 15)),
            ]);
        }
    }
}