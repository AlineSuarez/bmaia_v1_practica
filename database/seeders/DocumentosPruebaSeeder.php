<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\Visita;
use App\Models\VisitaGeneral;
use App\Models\VisitaInspeccion;
use App\Models\EstadoNutricional;
use App\Models\PresenciaVarroa;
use App\Models\PresenciaNosemosis;
use App\Models\CalidadReina;
use Carbon\Carbon;

class DocumentosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1;

        $apiarios = Apiario::with('colmenas')->get();

        foreach ($apiarios as $apiario) {
            $colmena = $apiario->colmenas->first();
            if (!$colmena) {
                $this->command->warn("❗ Apiario sin colmenas: ID {$apiario->id} ({$apiario->nombre})");
                continue;
            }

            $colmenaId = $colmena->id;

            // VISITA GENERAL
            $visita1 = Visita::create([
                'user_id' => $userId,
                'apiario_id' => $apiario->id,
                'colmena_id' => $colmenaId,
                'fecha_visita' => Carbon::now()->subDays(rand(1, 30)),
                'tipo_visita' => 'Visita General',
            ]);

            VisitaGeneral::create([
                'visita_id' => $visita1->id,
                'motivo' => 'Revisión general',
                'nombres' => 'María',
                'apellidos' => 'Soto',
                'rut' => '12.345.678-9',
                'telefono' => '+56912345678',
                'firma' => 'María Soto',
                'observacion_primera_visita' => 'Colmena en buen estado',
            ]);

            // INSPECCIÓN
            $visita2 = Visita::create([
                'user_id' => $userId,
                'apiario_id' => $apiario->id,
                'colmena_id' => $colmenaId,
                'fecha_visita' => Carbon::now()->subDays(rand(31, 60)),
                'tipo_visita' => 'Inspección de Visita',
            ]);

            VisitaInspeccion::create([
                'visita_id' => $visita2->id,
                'num_colmenas_totales' => 10,
                'num_colmenas_inspeccionadas' => 10,
                'num_colmenas_enfermas' => 1,
                'num_colmenas_activas' => 9,
                'num_colmenas_muertas' => 0,
                'flujo_nectar_polen' => 'Moderado',
                'nombre_revisor_apiario' => 'Inspector Pérez',
                'sospecha_enfermedad' => 'Leve presencia de varroa',
                'observaciones' => 'Se recomienda monitoreo',
            ]);

            // CALIDAD REINA
            CalidadReina::create([
                'colmena_id' => $colmenaId,
                'postura_reina' => 'Buena',
                'estado_cria' => 'Saludable',
                'postura_zanganos' => 'Presente',
                'origen_reina' => 'Natural',
                'raza' => 'Italiana',
                'linea_genetica' => 'Línea 2025',
                'fecha_introduccion' => Carbon::now()->subMonths(4),
                'estado_actual' => 'Activa',
                'reemplazos_realizados' => [
                    ['fecha' => Carbon::now()->subMonths(12)->format('Y-m-d'), 'motivo' => 'Reina envejecida']
                ],
                'visita_id' => null,
            ]);

            // ALIMENTACIÓN
            $visita3 = Visita::create([
                'user_id' => $userId,
                'apiario_id' => $apiario->id,
                'colmena_id' => $colmenaId,
                'fecha_visita' => Carbon::now()->subDays(rand(5, 20)),
                'tipo_visita' => 'Alimentación',
            ]);

            EstadoNutricional::create([
                'colmena_id' => $colmenaId,
                'visita_id' => $visita3->id,
                'tipo_alimentacion' => 'Jarabe concentrado',
                'fecha_aplicacion' => Carbon::now()->subDays(3),
                'insumo_utilizado' => 'Azúcar 2:1',
                'dosifiacion' => '600ml/colmena',
                'metodo_utilizado' => 'Jeringa',
                'objetivo' => 'Mantención',
            ]);

            // TRATAMIENTO VARROA
            $visita4 = Visita::create([
                'user_id' => $userId,
                'apiario_id' => $apiario->id,
                'colmena_id' => $colmenaId,
                'fecha_visita' => Carbon::now()->subDays(rand(10, 40)),
                'tipo_visita' => 'Tratamiento: Varroa',
            ]);

            PresenciaVarroa::create([
                'colmena_id' => $colmenaId,
                'visita_id' => $visita4->id,
                'diagnostico_visual' => 'Presencia de varroa en obreras',
                'muestreo_abejas_adultas' => '3 varroas/100 abejas',
                'muestreo_cria_operculada' => '10%',
                'tratamiento' => 'Ácido oxálico',
                'metodo_diagnostico' => 'Alcohol wash',
                'fecha_monitoreo_varroa' => Carbon::now()->subDays(7),
                'producto_comercial' => 'Apivar',
                'ingrediente_activo' => 'Amitraz',
                'fecha_aplicacion' => Carbon::now()->subDays(4),
                'dosificacion' => '2 tiras',
                'metodo_aplicacion' => 'Tiras',
                'periodo_carencia' => '14',
            ]);

            // TRATAMIENTO NOSEMA
            $visita5 = Visita::create([
                'user_id' => $userId,
                'apiario_id' => $apiario->id,
                'colmena_id' => $colmenaId,
                'fecha_visita' => Carbon::now()->subDays(rand(10, 40)),
                'tipo_visita' => 'Tratamiento: Nosema',
            ]);

            PresenciaNosemosis::create([
                'colmena_id' => $colmenaId,
                'visita_id' => $visita5->id,
                'signos_clinicos' => 'Heces en piquera',
                'muestreo_laboratorio' => 'Esporas elevadas',
                'metodo_diagnostico_laboratorio' => 'Microscopía',
                'fecha_monitoreo_nosema' => Carbon::now()->subDays(6),
                'producto_comercial' => 'Nosevit',
                'ingrediente_activo' => 'Fumagilina-B',
                'fecha_aplicacion' => Carbon::now()->subDays(3),
                'dosificacion' => '15ml',
                'metodo_aplicacion' => 'Jarabe',
            ]);

            // TRATAMIENTO OTRO (sin PCC)
            Visita::create([
                'user_id' => $userId,
                'apiario_id' => $apiario->id,
                'colmena_id' => $colmenaId,
                'fecha_visita' => Carbon::now()->subDays(12),
                'tipo_visita' => 'Tratamiento: Otro',
            ]);

            $this->command->info("✔️ Registros generados para Apiario ID {$apiario->id} ({$apiario->nombre})");
        }
    }
}
