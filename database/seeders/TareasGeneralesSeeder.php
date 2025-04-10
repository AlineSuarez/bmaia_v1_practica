<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TareasGeneralesSeeder extends Seeder
{
    public function run()
    {
        $etapas = [
            ["id" => 17, "nombre" => "INVERNADA", "estado" => "Pendiente", "created_at" => now(), "updated_at" => now()],
            ["id" => 18, "nombre" => "SERVICIO DE POLINIZACIÓN", "estado" => "Pendiente", "created_at" => now(), "updated_at" => now()],
            ["id" => 19, "nombre" => "APIARIO PARA MIEL - DESARROLLO CÁMARA DE CRÍA", "estado" => "Pendiente", "created_at" => now(), "updated_at" => now()],
            ["id" => 20, "nombre" => "MANTENCIÓN DE COLMENAS", "estado" => "Pendiente", "created_at" => now(), "updated_at" => now()],
            ["id" => 21, "nombre" => "COSECHA MIEL", "estado" => "Pendiente", "created_at" => now(), "updated_at" => now()],
            ["id" => 22, "nombre" => "PRE-INVERNADA", "estado" => "Pendiente", "created_at" => now(), "updated_at" => now()],
        ];

        foreach ($etapas as $etapa) {
            DB::table('tareas_generales')->updateOrInsert(
                ['id' => $etapa['id']],
                $etapa
            );
        }
    }
}
