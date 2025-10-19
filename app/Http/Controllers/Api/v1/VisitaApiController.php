<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\{
    Apiario,
    Visita,
    Colmena,
    EstadoNutricional,
    PresenciaVarroa,
    PresenciaNosemosis,
    CalidadReina,
    VisitaGeneral,
    VisitaInspeccion
};

class VisitaApiController extends Controller
{

    public function index(Request $request)
    {
        $q = Visita::query()->latest('fecha_visita');

        if ($request->filled('apiario_id')) {
            $q->where('apiario_id', $request->integer('apiario_id'));
        }

        // tipos esperados:
        // 'Visita General', 'Inspección de Visita', 'Uso de Medicamentos', 'Alimentación', 'Inspección de Reina'
        if ($request->filled('tipo')) {
            $q->where('tipo_visita', $request->string('tipo'));
        }

        return response()->json(['data' => $q->get()], 200);
    }

    // GET /api/v1/apiarios/{apiario}/visitas?tipo=...
    public function indexByApiario(Request $request, Apiario $apiario)
    {
        $q = Visita::where('apiario_id', $apiario->id)->latest('fecha_visita');

        if ($request->filled('tipo')) {
            $q->where('tipo_visita', $request->string('tipo'));
        }

        return response()->json(['data' => $q->get()], 200);
    }
   
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'apiario_id'    => 'required|integer|exists:apiarios,id',
            'fecha'         => 'required|date',
            'objetivo'      => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $visita = Visita::create([
            'apiario_id'   => $data['apiario_id'],
            'user_id'      => $request->user()->id,
            'fecha_visita' => $data['fecha'],
            'tipo_visita'  => 'Visita General',
            'observaciones'=> $data['observaciones'] ?? null,
        ]);

        return response()->json(['data' => $visita], 201);
    }
    public function update(Request $request, Visita $visita)
    {
        $data = $request->validate([
            'fecha'         => 'nullable|date',
            'observaciones' => 'nullable|string',
            'objetivo'      => 'nullable|string|max:255',
            'tipo_visita'   => 'nullable|string|max:255',
        ]);

        $visita->update([
            'fecha_visita' => $data['fecha'] ?? $visita->fecha_visita,
            'observaciones'=> $data['observaciones'] ?? $visita->observaciones,
            'tipo_visita'  => $data['tipo_visita'] ?? $visita->tipo_visita,
        ]);

        return response()->json(['data' => $visita], 200);
    }

    public function destroy(Visita $visita)
    {
        $visita->delete();
        return response()->json(['ok' => true], 200);
    }

    public function storeGeneral(Request $request)
    {
        $data = $request->validate([
            'apiario_id' => 'required|integer|exists:apiarios,id',
            'fecha'      => 'required|date',
            'motivo'     => 'required|string|max:255',
            'nombres'    => 'required|string|max:255',
            'apellidos'  => 'required|string|max:255',
            'rut'        => 'required|string|max:20',
            'telefono'   => 'required|string|max:20',
            'visita_id'  => 'nullable|exists:visitas,id',
        ]);

        $apiario = Apiario::findOrFail($data['apiario_id']);
        $visita = $data['visita_id']
            ? tap(Visita::where('id', $data['visita_id'])
                    ->where('apiario_id', $apiario->id)->firstOrFail()
                )->update([
                    'fecha_visita' => $data['fecha'],
                    'tipo_visita'  => 'Visita General',
                ])
            : Visita::create([
                'apiario_id'   => $apiario->id,
                'user_id'      => $request->user()->id,
                'fecha_visita' => $data['fecha'],
                'tipo_visita'  => 'Visita General',
            ]);
        $visita->visitaGeneral()->updateOrCreate([], [
            'motivo'    => $data['motivo'],
            'nombres'   => $data['nombres'],
            'apellidos' => $data['apellidos'],
            'rut'       => $data['rut'],
            'telefono'  => $data['telefono'],
        ]);

        return response()->json(['data' => $visita->load('visitaGeneral')], 201);
    }

    public function updateGeneral(Request $request, Visita $visita)
    {
        $data = $request->validate([
            'fecha'      => 'nullable|date',
            'motivo'     => 'nullable|string|max:255',
            'nombres'    => 'nullable|string|max:255',
            'apellidos'  => 'nullable|string|max:255',
            'rut'        => 'nullable|string|max:20',
            'telefono'   => 'nullable|string|max:20',
        ]);

        if (isset($data['fecha'])) {
            $visita->update([
                'fecha_visita' => $data['fecha'],
                'tipo_visita'  => 'Visita General',
            ]);
        }

        if (!empty(array_diff_key($data, ['fecha'=>true]))) {
            $visita->visitaGeneral()->updateOrCreate([], array_filter($data, fn($v,$k)=>$k!=='fecha' && $v!==null, ARRAY_FILTER_USE_BOTH));
        }

        return response()->json(['data' => $visita->load('visitaGeneral')], 200);
    }

    public function storeInspeccion(Request $request)
    {
        $data = $request->validate([
            'apiario_id'                   => 'required|integer|exists:apiarios,id',
            'fecha_inspeccion'             => 'required|date',
            'num_colmenas_totales'         => 'required|integer',
            'num_colmenas_activas'         => 'required|integer',
            'num_colmenas_enfermas'        => 'required|integer',
            'num_colmenas_muertas'         => 'required|integer',
            'num_colmenas_inspeccionadas'  => 'required|integer',
            'flujo_nectar_polen'           => 'required|string',
            'nombre_revisor_apiario'       => 'required|string',
            'sospecha_enfermedad'          => 'nullable|string',
            'observaciones'                => 'nullable|string',
            'visita_id'                    => 'nullable|exists:visitas,id',
        ]);

        $apiario = Apiario::findOrFail($data['apiario_id']);

        $visita = $data['visita_id']
            ? tap(Visita::findOrFail($data['visita_id']))->update([
                'fecha_visita' => $data['fecha_inspeccion'],
                'tipo_visita'  => 'Inspección de Visita',
            ])
            : Visita::create([
                'apiario_id'   => $apiario->id,
                'user_id'      => $request->user()->id,
                'fecha_visita' => $data['fecha_inspeccion'],
                'tipo_visita'  => 'Inspección de Visita',
            ]);

        $visita->inspeccion()->updateOrCreate([], [
            'num_colmenas_totales'        => $data['num_colmenas_totales'],
            'num_colmenas_activas'        => $data['num_colmenas_activas'],
            'num_colmenas_enfermas'       => $data['num_colmenas_enfermas'],
            'num_colmenas_muertas'        => $data['num_colmenas_muertas'],
            'num_colmenas_inspeccionadas' => $data['num_colmenas_inspeccionadas'],
            'flujo_nectar_polen'          => $data['flujo_nectar_polen'],
            'nombre_revisor_apiario'      => $data['nombre_revisor_apiario'],
            'sospecha_enfermedad'         => $data['sospecha_enfermedad'] ?? null,
            'observaciones'               => $data['observaciones'] ?? null,
        ]);

        return response()->json(['data' => $visita->load('inspeccion')], 201);
    }

    public function updateInspeccion(Request $request, Visita $visita)
    {
        $data = $request->validate([
            'fecha_inspeccion'             => 'nullable|date',
            'num_colmenas_totales'         => 'nullable|integer',
            'num_colmenas_activas'         => 'nullable|integer',
            'num_colmenas_enfermas'        => 'nullable|integer',
            'num_colmenas_muertas'         => 'nullable|integer',
            'num_colmenas_inspeccionadas'  => 'nullable|integer',
            'flujo_nectar_polen'           => 'nullable|string',
            'nombre_revisor_apiario'       => 'nullable|string',
            'sospecha_enfermedad'          => 'nullable|string',
            'observaciones'                => 'nullable|string',
        ]);

        if (isset($data['fecha_inspeccion'])) {
            $visita->update([
                'fecha_visita' => $data['fecha_inspeccion'],
                'tipo_visita'  => 'Inspección de Visita',
            ]);
        }

        $visita->inspeccion()->updateOrCreate([], array_filter($data, fn($v)=>!is_null($v)));
        return response()->json(['data' => $visita->load('inspeccion')], 200);
    }

    public function storeMedicamentos(Request $request)
    {
        $common = $request->validate([
            'apiario_id'         => 'required|integer|exists:apiarios,id',
            'fecha'              => 'required|date',
            'motivo_tratamiento' => 'required|in:varroa,nosema,otro',
            'motivo_otro'        => 'nullable|string|required_if:motivo_tratamiento,otro',
            'responsable'        => 'required|string',
            'observaciones'      => 'nullable|string',
            'visita_id'          => 'nullable|exists:visitas,id',
        ]);

        $apiario = Apiario::with('colmenas')->findOrFail($common['apiario_id']);

        $rulesVarroa = [
            'varroa_metodo_diagnostico'            => 'nullable|string',
            'varroa_diagnostico_visual'            => 'nullable|string',
            'varroa_muestreo_abejas_adultas'       => 'nullable|string',
            'varroa_muestreo_cria_operculada'      => 'nullable|string',
            'varroa_tratamiento'                   => 'nullable|string',
            'varroa_fecha_aplicacion'              => 'nullable|date',
            'varroa_dosificacion'                  => 'nullable|string',
            'varroa_metodo_aplicacion'             => 'nullable|string',
            'varroa_fecha_monitoreo_varroa'        => 'nullable|date',
            'varroa_producto_comercial'            => 'nullable|string',
            'varroa_ingrediente_activo'            => 'nullable|string',
            'varroa_periodo_carencia'              => 'nullable|integer',
        ];

        $rulesNosema = [
            'nosemosis_metodo_diagnostico_laboratorio' => 'nullable|string',
            'nosemosis_signos_clinicos'                => 'nullable|string',
            'nosemosis_muestreo_laboratorio'           => 'nullable|string',
            'nosemosis_tratamiento'                    => 'nullable|string',
            'nosemosis_fecha_aplicacion'               => 'nullable|date',
            'nosemosis_dosificacion'                   => 'nullable|string',
            'nosemosis_metodo_aplicacion'              => 'nullable|string',
            'nosemosis_fecha_monitoreo_nosema'         => 'nullable|date',
            'nosemosis_producto_comercial'             => 'nullable|string',
            'nosemosis_ingrediente_activo'             => 'nullable|string',
        ];

        $extra = match ($common['motivo_tratamiento']) {
            'varroa' => $request->validate($rulesVarroa),
            'nosema' => $request->validate($rulesNosema),
            default  => [],
        };

        $visita = $common['visita_id']
            ? tap(Visita::findOrFail($common['visita_id']))->update([
                'fecha_visita'       => $common['fecha'],
                'tipo_visita'        => 'Uso de Medicamentos',
                'motivo_tratamiento' => $common['motivo_tratamiento'],
                'motivo'             => $common['motivo_tratamiento'] === 'otro'
                                        ? ($common['motivo_otro'] ?? 'otro')
                                        : $common['motivo_tratamiento'],
                'responsable'        => $common['responsable'],
                'observaciones'      => $common['observaciones'] ?? null,
            ])
            : Visita::create([
                'apiario_id'         => $apiario->id,
                'user_id'            => $request->user()->id,
                'fecha_visita'       => $common['fecha'],
                'tipo_visita'        => 'Uso de Medicamentos',
                'motivo_tratamiento' => $common['motivo_tratamiento'],
                'motivo'             => $common['motivo_tratamiento'] === 'otro'
                                        ? ($common['motivo_otro'] ?? 'otro')
                                        : $common['motivo_tratamiento'],
                'responsable'        => $common['responsable'],
                'observaciones'      => $common['observaciones'] ?? null,
            ]);

        DB::transaction(function () use ($apiario, $visita, $common, $extra) {
            $firstId = null;

            if ($common['motivo_tratamiento'] === 'varroa') {
                foreach ($apiario->colmenas as $col) {
                    $pv = PresenciaVarroa::updateOrCreate(
                        ['visita_id' => $visita->id, 'colmena_id' => $col->id],
                        [
                            'metodo_diagnostico'      => $extra['varroa_metodo_diagnostico'] ?? null,
                            'diagnostico_visual'      => $extra['varroa_diagnostico_visual'] ?? null,
                            'muestreo_abejas_adultas' => $extra['varroa_muestreo_abejas_adultas'] ?? null,
                            'muestreo_cria_operculada'=> $extra['varroa_muestreo_cria_operculada'] ?? null,
                            'tratamiento'             => $extra['varroa_tratamiento'] ?? null,
                            'fecha_aplicacion'        => $extra['varroa_fecha_aplicacion'] ?? null,
                            'dosificacion'            => $extra['varroa_dosificacion'] ?? null,
                            'metodo_aplicacion'       => $extra['varroa_metodo_aplicacion'] ?? null,
                            'fecha_monitoreo_varroa'  => $extra['varroa_fecha_monitoreo_varroa'] ?? null,
                            'producto_comercial'      => $extra['varroa_producto_comercial'] ?? null,
                            'ingrediente_activo'      => $extra['varroa_ingrediente_activo'] ?? null,
                            'periodo_carencia'        => $extra['varroa_periodo_carencia'] ?? null,
                        ]
                    );
                    $firstId = $firstId ?? $pv->id;
                }
                $visita->update(['presencia_varroa_id' => $firstId]);
            }

            if ($common['motivo_tratamiento'] === 'nosema') {
                foreach ($apiario->colmenas as $col) {
                    $pn = PresenciaNosemosis::updateOrCreate(
                        ['visita_id' => $visita->id, 'colmena_id' => $col->id],
                        [
                            'metodo_diagnostico_laboratorio' => $extra['nosemosis_metodo_diagnostico_laboratorio'] ?? null,
                            'signos_clinicos'                => $extra['nosemosis_signos_clinicos'] ?? null,
                            'muestreo_laboratorio'           => $extra['nosemosis_muestreo_laboratorio'] ?? null,
                            'tratamiento'                    => $extra['nosemosis_tratamiento'] ?? null,
                            'fecha_aplicacion'               => $extra['nosemosis_fecha_aplicacion'] ?? null,
                            'dosificacion'                   => $extra['nosemosis_dosificacion'] ?? null,
                            'metodo_aplicacion'              => $extra['nosemosis_metodo_aplicacion'] ?? null,
                            'fecha_monitoreo_nosema'         => $extra['nosemosis_fecha_monitoreo_nosema'] ?? null,
                            'producto_comercial'             => $extra['nosemosis_producto_comercial'] ?? null,
                            'ingrediente_activo'             => $extra['nosemosis_ingrediente_activo'] ?? null,
                        ]
                    );
                    $firstId = $firstId ?? $pn->id;
                }
                $visita->update(['presencia_nosemosis_id' => $firstId]);
            }
        });

        return response()->json(['data' => $visita->fresh()], 201);
    }

    public function updateMedicamentos(Request $request, Visita $visita)
    {
        $request->merge(['visita_id' => $visita->id, 'apiario_id' => $visita->apiario_id]);
        return $this->storeMedicamentos($request);
    }

    public function storeAlimentacion(Request $request)
    {
        $data = $request->validate([
            'apiario_id'                        => 'required|integer|exists:apiarios,id',
            'objetivo'                          => 'required|in:estimulacion,mantencion',
            'tipo_alimentacion'                 => 'required|string|max:255',
            'fecha_aplicacion_insumo_utilizado' => 'required|date',
            'insumo_utilizado'                  => 'nullable|string|max:255',
            'dosificacion'                      => 'nullable|string|max:255',
            'metodo_utilizado'                  => 'required|string|max:255',
            'visita_id'                         => 'nullable|exists:visitas,id',
        ]);

        $apiario = Apiario::with('colmenas')->findOrFail($data['apiario_id']);

        $visita = $data['visita_id']
            ? Visita::findOrFail($data['visita_id'])
            : Visita::create([
                'apiario_id'   => $apiario->id,
                'user_id'      => $request->user()->id,
                'tipo_visita'  => 'Alimentación',
                'fecha_visita' => now(),
            ]);

        DB::transaction(function () use ($apiario, $visita, $data) {
            $firstId = null;
            foreach ($apiario->colmenas as $col) {
                $en = EstadoNutricional::updateOrCreate(
                    ['visita_id' => $visita->id, 'colmena_id' => $col->id],
                    [
                        'objetivo'          => $data['objetivo'],
                        'tipo_alimentacion' => $data['tipo_alimentacion'],
                        'fecha_aplicacion'  => $data['fecha_aplicacion_insumo_utilizado'],
                        'insumo_utilizado'  => $data['insumo_utilizado'] ?? null,
                        'dosifiacion'       => $data['dosificacion'] ?? null, // columna se llama 'dosifiacion'
                        'metodo_utilizado'  => $data['metodo_utilizado'],
                    ]
                );
                $firstId = $firstId ?? $en->id;
            }
            $visita->update(['estado_nutricional_id' => $firstId]);
        });

        return response()->json(['data' => $visita->fresh()], 201);
    }

    public function updateAlimentacion(Request $request, Visita $visita)
    {
        $request->merge(['visita_id' => $visita->id, 'apiario_id' => $visita->apiario_id]);
        return $this->storeAlimentacion($request);
    }

    public function storeReina(Request $request)
    {
        $data = $request->validate([
            'apiario_id'                           => 'required|integer|exists:apiarios,id',
            'calidad_reina.postura_reina'          => 'nullable|string|max:255',
            'calidad_reina.estado_cria'            => 'nullable|string|max:255',
            'calidad_reina.postura_zanganos'       => 'nullable|string|max:255',
            'calidad_reina.origen_reina'           => 'nullable|in:natural,comprada,fecundada,virgen',
            'calidad_reina.raza'                   => 'nullable|string|max:255',
            'calidad_reina.linea_genetica'         => 'nullable|string|max:255',
            'calidad_reina.fecha_introduccion'     => 'nullable|date',
            'calidad_reina.estado_actual'          => 'nullable|in:activa,fallida,reemplazada',
            'calidad_reina.reemplazos_realizados'  => 'nullable|array',
            'visita_id'                            => 'nullable|exists:visitas,id',
        ]);

        $apiario = Apiario::with('colmenas')->findOrFail($data['apiario_id']);
        $cr = $data['calidad_reina'] ?? [];

        $reemplazos = null;
        if (isset($cr['reemplazos_realizados'])) {
            $limpios = array_filter($cr['reemplazos_realizados'], fn($r) =>
                !empty($r['fecha'] ?? null) || !empty($r['motivo'] ?? null)
            );
            $reemplazos = $limpios ?: null;
        }

        $visita = $data['visita_id']
            ? tap(Visita::findOrFail($data['visita_id']))->update([
                'fecha_visita' => now(),
                'tipo_visita'  => 'Inspección de Reina',
            ])
            : Visita::create([
                'apiario_id'   => $apiario->id,
                'user_id'      => $request->user()->id,
                'fecha_visita' => now(),
                'tipo_visita'  => 'Inspección de Reina',
            ]);

        DB::transaction(function () use ($apiario, $visita, $cr, $reemplazos) {
            $ids = [];
            foreach ($apiario->colmenas as $col) {
                $row = CalidadReina::updateOrCreate(
                    ['colmena_id' => $col->id, 'visita_id' => $visita->id],
                    [
                        'postura_reina'         => $cr['postura_reina'] ?? null,
                        'estado_cria'           => $cr['estado_cria'] ?? null,
                        'postura_zanganos'      => $cr['postura_zanganos'] ?? null,
                        'origen_reina'          => $cr['origen_reina'] ?? null,
                        'raza'                  => $cr['raza'] ?? null,
                        'linea_genetica'        => $cr['linea_genetica'] ?? null,
                        'fecha_introduccion'    => $cr['fecha_introduccion'] ?? null,
                        'estado_actual'         => $cr['estado_actual'] ?? null,
                        'reemplazos_realizados' => $reemplazos,
                    ]
                );
                $ids[] = $row->id;
            }
            if (!empty($ids)) {
                $visita->update(['calidad_reina_id' => $ids[0]]);
            }
        });

        return response()->json(['data' => $visita->fresh()], 201);
    }

    public function updateReina(Request $request, Visita $visita)
    {
        $request->merge(['visita_id' => $visita->id, 'apiario_id' => $visita->apiario_id]);
        return $this->storeReina($request);
    }
}
