<?php

namespace Database\Seeders;

use App\Models\Fenofase;
use Illuminate\Database\Seeder;

class FenofasesSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['clave'=>'boton',   'nombre'=>'Botón floral',        'orden'=>1],
            ['clave'=>'inicio',  'nombre'=>'Inicio de floración', 'orden'=>2],
            ['clave'=>'plena',   'nombre'=>'Plena floración',     'orden'=>3],
            ['clave'=>'terminal','nombre'=>'Floración terminal',  'orden'=>4],
        ];
        foreach ($rows as $r) Fenofase::updateOrCreate(['clave'=>$r['clave']], $r);
    }
}
