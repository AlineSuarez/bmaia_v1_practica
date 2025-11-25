<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlorFaseDuracionesSeeder extends Seeder
{
    public function run(): void
    {
        // Mapa: slug de flor => offsets (días desde 'boton')
        $data = [
            'avellano' => ['inicio'=>7,  'plena'=>21, 'terminal'=>35],
            'boldo'    => ['inicio'=>10, 'plena'=>24, 'terminal'=>40],
            'litre'    => ['inicio'=>8,  'plena'=>20, 'terminal'=>32],
            'peumo'    => ['inicio'=>9,  'plena'=>23, 'terminal'=>38],
            'quillay'  => ['inicio'=>6,  'plena'=>18, 'terminal'=>30],
            'tiahue'   => ['inicio'=>7,  'plena'=>19, 'terminal'=>31],
            'tevo'     => ['inicio'=>7,  'plena'=>21, 'terminal'=>33],
        ];

        // Resolvemos ids por slug
        $flores = DB::table('flores')->select('id','slug')->get()->keyBy('slug');

        $rows = [];
        foreach ($data as $slug => $fasos) {
            if (!$flores->has($slug)) continue;
            $florId = $flores[$slug]->id;

            foreach ($fasos as $faseClave => $dias) {
                $rows[] = [
                    'flor_id'      => $florId,
                    'fase_clave'   => $faseClave,     // inicio|plena|terminal
                    'offset_dias'  => $dias,
                    'fuente'       => 'Referencia interna',
                    'nota'         => null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
        }

        // upsert para poder re-correr el seeder sin duplicar
        foreach ($rows as $r) {
            DB::table('flor_fase_duraciones')->updateOrInsert(
                ['flor_id'=>$r['flor_id'], 'fase_clave'=>$r['fase_clave']],
                $r
            );
        }
    }
}
