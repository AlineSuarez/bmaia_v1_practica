<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MigrateVisitaDataToSubtables extends Migration
{
    public function up()
    {
        $visitas = DB::table('visitas')->get();

        foreach ($visitas as $visita) {
            if ($visita->tipo_visita === 'Visita General') {
                DB::table('visita_generales')->insert([
                    'visita_id' => $visita->id,
                    'motivo' => $visita->motivo,
                    'nombres' => $visita->nombres,
                    'apellidos' => $visita->apellidos,
                    'rut' => $visita->rut,
                    'telefono' => $visita->telefono,
                    'firma' => $visita->firma,
                    'observacion_primera_visita' => $visita->observacion_primera_visita,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($visita->tipo_visita === 'Inspección de Visita') {
                DB::table('visita_inspecciones')->insert([
                    'visita_id' => $visita->id,
                    'num_colmenas_totales' => $visita->num_colmenas_totales,
                    'num_colmenas_inspeccionadas' => $visita->num_colmenas_inspeccionadas,
                    'num_colmenas_enfermas' => $visita->num_colmenas_enfermas,
                    'num_colmenas_activas' => $visita->num_colmenas_activas,
                    'num_colmenas_muertas' => $visita->num_colmenas_muertas,
                    'flujo_nectar_polen' => $visita->flujo_nectar_polen,
                    'nombre_revisor_apiario' => $visita->nombre_revisor_apiario,
                    'sospecha_enfermedad' => $visita->sospecha_enfermedad,
                    'observaciones' => $visita->observaciones,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        DB::table('visita_generales')->truncate();
        DB::table('visita_inspecciones')->truncate();
    }
}
