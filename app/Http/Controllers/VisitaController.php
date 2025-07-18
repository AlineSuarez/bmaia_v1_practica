<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\{
    Apiario,
    Visita,
    EstadoNutricional,
    SistemaExperto,
    PresenciaVarroa,
    PresenciaNosemosis,
    Colmena,
    DesarrolloCria,
    CalidadReina,
    IndiceCosecha,
    PreparacionInvernada
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VisitaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['publicView']);
    }

    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;

        // 1) Fijos
        $apiariosFijos = Apiario::with('comuna', 'ultimaVisita')
            ->where('user_id', $userId)
            ->where('tipo_apiario', 'fijo')
            ->get();

        // 2) Trashumantes “base” (NO temporales)
        $apiariosBase = Apiario::with('ultimaVisita')
            ->where('user_id', $userId)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->where('es_temporal', false)
            ->get();

        // 3) Apiarios temporales (los que creaste con el wizard)
        $apiariosTemporales = Apiario::with('ultimaVisita')
            ->where('user_id', $userId)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->where('es_temporal', true)
            ->get();

        // 4) Apiarios Archivados → aquellos que ya no estén activos (activo = 0)
        $apiariosArchivados = Apiario::where('user_id', $userId)
            ->where('tipo_apiario', 'trashumante')
            ->where('es_temporal', true)
            ->where('activo', 0)
            ->with('comuna.region', 'ultimoMovimientoDestino.apiarioOrigen.comuna.region')
            ->orderByDesc('updated_at')
            ->get();

        return view('visitas.index', compact(
            'apiariosFijos',
            'apiariosBase',
            'apiariosTemporales',
            'apiariosArchivados',
            'user'
        ));
    }

    public function show(Apiario $apiario)
    {
        // 1) PCC de sistema experto
        $pccs = SistemaExperto::where('colmena_id', $colmena->id)
            ->with([
                'desarrolloCria',
                'calidadReina',
                'estadoNutricional',
                'presenciaVarroa',
                'presenciaNosemosis',
                'indiceCosecha',
                'preparacionInvernada'
            ])
            ->orderByDesc('fecha')
            ->get();

        // 2) Últimos registros de campo
        $lastAlimentacion = EstadoNutricional::where('colmena_id', $colmena->id)
            ->orderByDesc('created_at')->first();
        $lastVarroa = PresenciaVarroa::where('colmena_id', $colmena->id)
            ->orderByDesc('created_at')->first();
        $lastNosemosis = PresenciaNosemosis::where('colmena_id', $colmena->id)
            ->orderByDesc('created_at')->first();

        return view(
            'colmenas.show',
            compact(
                'apiario',
                'colmena',
                'pccs',
                'lastAlimentacion',
                'lastVarroa',
                'lastNosemosis'
            )
        );
    }

    public function create($id_apiario)
    {
        $user = auth()->user();
        $apiario = Apiario::where('user_id', auth()->id())
                        ->findOrFail($id_apiario);

        $visita = null;
        if ($id = request('visita_id')) {
            $visita = Visita::where('id', $id)
                            ->where('apiario_id', $id_apiario)
                            ->where('tipo_visita', 'Inspección de Visita')
                            ->firstOrFail();
        }

        return view('visitas.create', compact('apiario', 'visita'));
    }

    public function createMedicamentos($id_apiario)
    {
        $user = auth()->user();
        $apiario = Apiario::where('user_id', auth()->id())->where('id', $id_apiario)->firstOrFail();
        return view('visitas.create2', compact('apiario', 'user'));
    }

    public function createGeneral($id_apiario)
    {
        $apiario = Apiario::where('user_id', auth()->id())
                        ->findOrFail($id_apiario);

        // Sólo cargo si me pasan visita_id
        $visita = null;
        if ($visitaId = request()->get('visita_id')) {
            $visita = Visita::where('id', $visitaId)
                            ->where('apiario_id', $apiario->id)
                            ->where('tipo_visita', 'Visita General')
                            ->firstOrFail();
        }

        return view('visitas.create1', compact('apiario', 'visita'));
    }

    public function createAlimentacion($id_apiario)
    {
        $apiario = Apiario::with('colmenas')
            ->where('user_id', auth()->id())
            ->findOrFail($id_apiario);

        return view('visitas.create3', compact('apiario'));
    }

    public function store(Request $request, Apiario $apiario)
    {
        $data = $request->validate([
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
        ]);

        // Si viene visita_id, actualizamos
        if ($id = $request->visita_id) {
            $visita = Visita::findOrFail($id);
            $visita->update([
                'fecha_visita'               => $data['fecha_inspeccion'],
                'num_colmenas_totales'       => $data['num_colmenas_totales'],
                'num_colmenas_activas'       => $data['num_colmenas_activas'],
                'num_colmenas_enfermas'      => $data['num_colmenas_enfermas'],
                'num_colmenas_muertas'       => $data['num_colmenas_muertas'],
                'num_colmenas_inspeccionadas'=> $data['num_colmenas_inspeccionadas'],
                'flujo_nectar_polen'         => $data['flujo_nectar_polen'],
                'nombre_revisor_apiario'     => $data['nombre_revisor_apiario'],
                'sospecha_enfermedad'        => $data['sospecha_enfermedad'],
                'observaciones'              => $data['observaciones'],
            ]);

            return redirect()
                ->route('visitas.historial', $apiario)
                ->with('success', 'Inspección actualizada correctamente.');
        }

        // Si no, creamos nueva
        Visita::create(array_merge($data, [
            'apiario_id'   => $apiario->id,
            'fecha_visita' => $data['fecha_inspeccion'],
            'tipo_visita'  => 'Inspección de Visita',
        ]));

        return redirect()
            ->route('visitas.historial', $apiario)
            ->with('success', 'Inspección registrada correctamente.');
    }

    public function createReina($id_apiario)
    {
        $apiario = Apiario::where('user_id', auth()->id())->findOrFail($id_apiario);

        $visita = null;
        if ($id = request('visita_id')) {
            $visita = Visita::where('id', $id)
                            ->where('apiario_id', $id_apiario)
                            ->where('tipo_visita', 'Inspección de Reina')
                            ->firstOrFail();
        }

        return view('visitas.create4', compact('apiario', 'visita'));
    }


    public function storeMedicamentos(Request $request, Apiario $apiario)
    {
        // 1) validación común
        $commonData = $request->validate([
            'fecha' => 'required|date',
            'motivo_tratamiento' => 'required|in:varroa,nosema,otro',
            'motivo_otro' => 'nullable|string|required_if:motivo_tratamiento,otro',
            'responsable' => 'required|string',
            'nombre_comercial_medicamento' => 'nullable|string',
            'principio_activo_medicamento' => 'nullable|string',
            'periodo_resguardo' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        // 2) validación PCC condicional
        $pccRules = [];
        if ($commonData['motivo_tratamiento'] === 'varroa') {
            $pccRules = [
                'varroa_metodo_diagnostico'            => 'required|string',
                'varroa_diagnostico_visual'            => 'nullable|string',
                'varroa_muestreo_abejas_adultas'       => 'nullable|string',
                'varroa_muestreo_cria_operculada'      => 'nullable|string',
                'varroa_tratamiento'                   => 'nullable|string',
                'varroa_fecha_aplicacion'              => 'nullable|date',
                'varroa_dosificacion'                  => 'nullable|string',
                'varroa_metodo_aplicacion'             => 'nullable|string',
                'varroa_fecha_monitoreo_varroa'        => 'nullable|date',
                //'varroa_producto_comercial'            => 'nullable|string',
                //'varroa_ingrediente_activo'            => 'nullable|string',
                //'varroa_periodo_carencia'              => 'nullable|integer',
            ];
        } elseif ($commonData['motivo_tratamiento'] === 'nosema') {
            $pccRules = [
                'nosemosis_metodo_diagnostico_laboratorio' => 'required|string',
                'nosemosis_signos_clinicos'                => 'nullable|string',
                'nosemosis_muestreo_laboratorio'           => 'nullable|string',
                'nosemosis_tratamiento'                    => 'nullable|string',
                'nosemosis_fecha_aplicacion'               => 'nullable|date',
                'nosemosis_dosificacion'                   => 'nullable|string',
                'nosemosis_metodo_aplicacion'              => 'nullable|string',
                'nosemosis_fecha_monitoreo_nosema'         => 'nullable|date',
                //'nosemosis_producto_comercial'             => 'nullable|string',
                //'nosemosis_ingrediente_activo'             => 'nullable|string',
            ];
        }

        $pccData = $pccRules ? $request->validate($pccRules) : [];

        $data = array_merge($commonData, $pccData);
        DB::transaction(function () use ($data, $apiario, $request) {
            // 2) Crear únicamente una Visita o actualizarla
            if ($request->filled('visita_id')) {
                $visita = Visita::findOrFail($request->visita_id);
                $visita->update([
                    'fecha_visita'                 => $data['fecha'],
                    'motivo_tratamiento'           => $data['motivo_tratamiento'],
                    'motivo'                       => $data['motivo_tratamiento']==='otro'
                                                    ? $data['motivo_otro']
                                                    : $data['motivo_tratamiento'],
                    'nombre_comercial_medicamento' => $data['nombre_comercial_medicamento'],
                    'principio_activo_medicamento' => $data['principio_activo_medicamento'],
                    'periodo_resguardo'            => $data['periodo_resguardo'],
                    'responsable'                  => $data['responsable'],
                    'observaciones'                => $data['observaciones'] ?? null,
                ]);
            } else {
                $visita = Visita::create([
                    'apiario_id'                   => $apiario->id,
                    'user_id'                      => auth()->id(),
                    'fecha_visita'                 => $data['fecha'],
                    'tipo_visita'                  => 'Uso de Medicamentos',
                    'motivo_tratamiento'           => $data['motivo_tratamiento'],
                    'motivo'                       => $data['motivo_tratamiento']==='otro'
                                                    ? $data['motivo_otro']
                                                    : $data['motivo_tratamiento'],
                    'nombre_comercial_medicamento' => $data['nombre_comercial_medicamento'],
                    'principio_activo_medicamento' => $data['principio_activo_medicamento'],
                    'periodo_resguardo'            => $data['periodo_resguardo'],
                    'responsable'                  => $data['responsable'],
                    'observaciones'                => $data['observaciones'] ?? null,
                ]);
            }

            // 3) Para cada colmena: updateOrCreate en PCC varroa o nosemosis
            $firstId = null;
            if ($data['motivo_tratamiento'] === 'varroa') {
                foreach ($apiario->colmenas as $col) {
                    $pv = PresenciaVarroa::updateOrCreate(
                        ['visita_id'=>$visita->id, 'colmena_id'=>$col->id],
                        array_merge($pcc4Defaults = [
                            'n_colmenas_tratadas'=>1
                        ], [
                            'metodo_diagnostico'      => $data['varroa_metodo_diagnostico'],
                            'diagnostico_visual'      => $data['varroa_diagnostico_visual'] ?? null,
                            'muestreo_abejas_adultas' => $data['varroa_muestreo_abejas_adultas'] ?? null,
                            'muestreo_cria_operculada'=> $data['varroa_muestreo_cria_operculada'] ?? null,
                            'tratamiento'             => $data['varroa_tratamiento'] ?? null,
                            'fecha_aplicacion'        => $data['varroa_fecha_aplicacion'] ?? null,
                            'dosificacion'            => $data['varroa_dosificacion'] ?? null,
                            'metodo_aplicacion'       => $data['varroa_metodo_aplicacion'] ?? null,
                            'fecha_monitoreo_varroa'  => $data['varroa_fecha_monitoreo_varroa'] ?? null,
                            //'producto_comercial'      => $data['varroa_producto_comercial'] ?? null,
                            //'ingrediente_activo'      => $data['varroa_ingrediente_activo'] ?? null,
                            //'periodo_carencia'        => $data['varroa_periodo_carencia'] ?? null,
                        ])
                    );
                    $firstId = $firstId ?? $pv->id;
                }
                $visita->update(['presencia_varroa_id'=>$firstId]);
            }
            elseif ($data['motivo_tratamiento'] === 'nosema') {
                foreach ($apiario->colmenas as $col) {
                    $pn = PresenciaNosemosis::updateOrCreate(
                        ['visita_id'=>$visita->id, 'colmena_id'=>$col->id],
                        array_merge(['num_colmenas_tratadas'=>1], [
                            'metodo_diagnostico_laboratorio'=> $data['nosemosis_metodo_diagnostico_laboratorio'],
                            'signos_clinicos'               => $data['nosemosis_signos_clinicos'] ?? null,
                            'muestreo_laboratorio'          => $data['nosemosis_muestreo_laboratorio'] ?? null,
                            'tratamiento'                   => $data['nosemosis_tratamiento'] ?? null,
                            'fecha_aplicacion'              => $data['nosemosis_fecha_aplicacion'] ?? null,
                            'dosificacion'                  => $data['nosemosis_dosificacion'] ?? null,
                            'metodo_aplicacion'             => $data['nosemosis_metodo_aplicacion'] ?? null,
                            'fecha_monitoreo_nosema'        => $data['nosemosis_fecha_monitoreo_nosema'] ?? null,
                            //'producto_comercial'            => $data['nosemosis_producto_comercial'] ?? null,
                            //'ingrediente_activo'            => $data['nosemosis_ingrediente_activo'] ?? null,
                        ])
                    );
                    $firstId = $firstId ?? $pn->id;
                }
                $visita->update(['presencia_nosemosis_id'=>$firstId]);
            }
        });

        return redirect()
            ->route('visitas.historial', $apiario)
            ->with('success','Registro de uso de medicamentos guardado correctamente.');
    }


    public function editMedicamentos($id_apiario, $id_visita)
    {
        $apiario = Apiario::where('user_id', auth()->id())->findOrFail($id_apiario);
        $visita  = Visita::findOrFail($id_visita);

        // Inicializa a null por si acaso
        $pcc4 = null;
        $pcc5 = null;

        if ($visita->motivo_tratamiento === 'varroa') {
            // carga el registro varroa
            $pcc4 = PresenciaVarroa::where('visita_id', $visita->id)->first();
        } elseif ($visita->motivo_tratamiento === 'nosema') {
            // carga el registro nosemosis
            $pcc5 = PresenciaNosemosis::where('visita_id', $visita->id)->first();
        }

        return view('visitas.create2', compact('apiario', 'visita', 'pcc4', 'pcc5'));
    }

    public function storeGeneral(Request $request, Apiario $apiario)
    {
        $data = $request->validate([
            'fecha'  => 'required|date',
            'motivo' => 'required|string',
        ]);

        $fields = [
            'apiario_id'   => $apiario->id,
            'fecha_visita' => $data['fecha'],
            'motivo'       => $data['motivo'],
            'tipo_visita'  => 'Visita General',
            'nombres'      => auth()->user()->name,
            'apellidos'    => auth()->user()->last_name,
            'rut'          => auth()->user()->rut,
            'telefono'     => auth()->user()->telefono,
            'firma'        => auth()->user()->firma,
        ];

        if ($request->filled('visita_id')) {
            // ACTUALIZAR
            $visita = Visita::where('id', $request->visita_id)
                            ->where('apiario_id', $apiario->id)
                            ->firstOrFail();
            $visita->update($fields);
            $mensaje = 'Visita general actualizada correctamente.';
        } else {
            // CREAR
            Visita::create($fields);
            $mensaje = 'Visita general registrada correctamente.';
        }

        return redirect()
            ->route('visitas.historial', $apiario)
            ->with('success', $mensaje);
    }

    public function storeAlimentacion(Request $request, Apiario $apiario)
    {
        $data = $request->validate([
            'objetivo'                          => 'required|in:estimulacion,mantencion',
            'tipo_alimentacion'                 => 'required|string|max:255',
            'fecha_aplicacion_insumo_utilizado' => 'required|date',
            'insumo_utilizado'                  => 'nullable|string|max:255',
            'dosificacion'                      => 'nullable|string|max:255',
            'metodo_utilizado'                  => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($data, $apiario, $request) {
            $visita = $request->filled('visita_id')
                ? Visita::findOrFail($request->visita_id)
                : Visita::create([
                    'apiario_id'   => $apiario->id,
                    'user_id'      => auth()->id(),
                    'tipo_visita'  => 'Alimentación',
                    'fecha_visita' => now(),
                ]);

            $firstId = null;
            foreach ($apiario->colmenas as $colmena) {
                $en = EstadoNutricional::updateOrCreate(
                    ['visita_id'  => $visita->id, 'colmena_id' => $colmena->id],
                    [
                        'objetivo'            => $data['objetivo'],
                        'tipo_alimentacion'   => $data['tipo_alimentacion'],
                        'fecha_aplicacion'    => $data['fecha_aplicacion_insumo_utilizado'],
                        'insumo_utilizado'    => $data['insumo_utilizado'],
                        'dosifiacion'         => $data['dosificacion'],
                        'metodo_utilizado'    => $data['metodo_utilizado'],
                        'n_colmenas_tratadas' => 1,
                    ]
                );
                $firstId = $firstId ?? $en->id;
            }
            $visita->update(['estado_nutricional_id' => $firstId]);
        });

        return redirect()
            ->route('visitas.historial', $apiario)
            ->with('success', 'Registro de alimentación guardado correctamente.');
    }

    public function editAlimentacion(Apiario $apiario, Visita $visita)
    {
        // 1) pertenece al apiario y es del tipo correcto
        if ($visita->apiario_id !== $apiario->id || $visita->tipo_visita !== 'Alimentación') {
            abort(404);
        }

        // 2) carga el primer EstadoNutricional
        $estado = EstadoNutricional::where('visita_id', $visita->id)->first();

        // 3) llama a la vista de create3.blade (reutilizada para edit)
        return view('visitas.create3', compact('apiario', 'visita', 'estado'));
    }

    public function storeReina(Request $request, Apiario $apiario)
    {
        // Validar los datos del formulario
        $data = $request->validate([
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

        // Extraer los datos de calidad_reina
        $calidadReinaData = $data['calidad_reina'] ?? [];

        // Procesar reemplazos realizados si existen
        $reemplazosRealizados = null;
        if (isset($calidadReinaData['reemplazos_realizados'])) {
            // Filtrar reemplazos que tengan al menos fecha o motivo
            $reemplazos = array_filter($calidadReinaData['reemplazos_realizados'], function($reemplazo) {
                return !empty($reemplazo['fecha']) || !empty($reemplazo['motivo']);
            });
            $reemplazosRealizados = !empty($reemplazos) ? $reemplazos : null;
        }

        DB::transaction(function () use ($request, $apiario, $calidadReinaData, $reemplazosRealizados) {
            // Si viene visita_id, actualizamos la visita existente
            if ($request->filled('visita_id')) {
                $visita = Visita::findOrFail($request->visita_id);
                $visita->update([
                    'fecha_visita' => now(),
                    'tipo_visita'  => 'Inspección de Reina',
                ]);
            } else {
                // Crear una nueva visita
                $visita = Visita::create([
                    'apiario_id'   => $apiario->id,
                    'user_id'      => auth()->id(),
                    'fecha_visita' => now(),
                    'tipo_visita'  => 'Inspección de Reina',
                ]);
            }

            // Guardar un registro de calidad_reina para cada colmena del apiario
            $calidadReinaIds = [];
            foreach ($apiario->colmenas as $colmena) {
                $calidadReina = CalidadReina::updateOrCreate(
                    [
                        'colmena_id' => $colmena->id,
                        'visita_id'  => $visita->id,
                    ],
                    [
                        'postura_reina'         => $calidadReinaData['postura_reina'] ?? null,
                        'estado_cria'           => $calidadReinaData['estado_cria'] ?? null,
                        'postura_zanganos'      => $calidadReinaData['postura_zanganos'] ?? null,
                        'origen_reina'          => $calidadReinaData['origen_reina'] ?? null,
                        'raza'                  => $calidadReinaData['raza'] ?? null,
                        'linea_genetica'        => $calidadReinaData['linea_genetica'] ?? null,
                        'fecha_introduccion'    => $calidadReinaData['fecha_introduccion'] ?? null,
                        'estado_actual'         => $calidadReinaData['estado_actual'] ?? null,
                        'reemplazos_realizados' => $reemplazosRealizados,
                    ]
                );

                // Almacenar los IDs de las reinas creadas
                $calidadReinaIds[] = $calidadReina->id;
            }

            // Relacionar el primer registro de calidad_reina con la visita
            if (!empty($calidadReinaIds)) {
                $visita->calidad_reina_id = $calidadReinaIds[0];
                $visita->save();
            }
        });

        return redirect()->route('visitas.historial', $apiario)
            ->with('success', 'Registro de Calidad de Reina guardado correctamente.');
    }

    public function editReina($id_apiario, $visita_id)
    {
        $apiario = Apiario::where('user_id', auth()->id())->findOrFail($id_apiario);
        $visita = Visita::where('id', $visita_id)->where('apiario_id', $id_apiario)->firstOrFail();

        $calidadReina = CalidadReina::where('visita_id', $visita_id)->first();

        return view('visitas.create4', compact('apiario', 'visita', 'calidadReina'));
    }


    public function showHistorial($apiarioId)
    {
        // Obtener el apiario y sus visitas
        $apiario = Apiario::with('visitas.usuario')->findOrFail($apiarioId);
        // Retornar la vista de historial con los datos del apiario y sus visitas
        return view('visitas.historial', compact('apiario'));
    }

    public function createPcc(Visita $visita)
    {
        // 1) Recojo la colmena seleccionada por query-string ?colmena=XX
        $colmenaId = request('colmena');
        $colmena = Colmena::findOrFail($colmenaId);
        // 2) Fuerzo que la visita apunte a esa colmena
        if ($visita->colmena_id !== $colmenaId) {
            $visita->colmena_id = $colmenaId;
            $visita->save();
        }
        // arrays vacíos para no romper el blade
        $valores = [
            'desarrollo_cria' => [],
            'calidad_reina' => [],
            'estado_nutricional' => [],
            'presencia_varroa' => [],
            'presencia_nosemosis' => [],
            'indice_cosecha' => [],
            'preparacion_invernada' => [],
        ];

        return view('visitas.pcc_edit', compact('visita', 'colmena', 'valores'));
    }

    public function storePcc(Request $request, Visita $visita)
    {
        // 1) Validar colmena_id + arrays
        $data = $request->validate([
            'colmena_id' => 'required|integer|exists:colmenas,id',
            'desarrollo_cria' => 'array',
            'calidad_reina' => 'array',
            'estado_nutricional' => 'array',
            'presencia_varroa' => 'array',
            'presencia_nosemosis' => 'array',
            'indice_cosecha' => 'array',
            'preparacion_invernada' => 'array',
        ]);

        $colmenaId = $data['colmena_id'];

        DB::transaction(function () use ($data, $visita, $colmenaId) {
            // 2) Siempre ligamos la visita a esa colmena
            $visita->colmena_id = $colmenaId;
            $visita->fecha_visita = now();
            $visita->save();

            // 2) Para cada PCC: solo guardar si hay algún valor realmente útil
            $pccCampos = [
                'desarrollo_cria' => DesarrolloCria::class,
                'calidad_reina' => CalidadReina::class,
                'estado_nutricional' => EstadoNutricional::class,
                'presencia_varroa' => PresenciaVarroa::class,
                'presencia_nosemosis' => PresenciaNosemosis::class,
                'indice_cosecha' => IndiceCosecha::class,
                'preparacion_invernada' => PreparacionInvernada::class,
            ];

            foreach ($pccCampos as $clave => $modelo) {
                if (! array_key_exists($clave, $data)) {
                    continue;
                }

                // Filtrar los datos eliminando claves vacías o nulas
                $datos = array_filter(
                    $data[$clave] ?? [],
                    fn($valor) => !is_null($valor) && $valor !== ''
                );

                if (! empty($datos)) {
                    $attributes = [
                        'colmena_id' => $colmenaId,
                        'visita_id'  => $visita->id,
                    ];
                    $values = array_merge($datos, $attributes);

                    $modelo::updateOrCreate(
                        $attributes,
                        $values
                    );
                }
            }
        });

        return redirect()
            ->route('colmenas.show', [
                'apiario' => $visita->apiario_id,
                'colmena' => $colmenaId,
            ])
            ->with('success', 'PCC registrado correctamente.');
    }

    public function editPcc(Visita $visita)
    {
        // 1) Recojo la colmena seleccionada por query-string ?colmena=XX
        $colmenaId = request('colmena');
        $colmena   = Colmena::findOrFail($colmenaId);

        // 2) Para cada tabla hija, cargo el registro que tenga colmena_id + visita_id
        $pccMap = [
            'desarrollo_cria'       => DesarrolloCria::class,
            'calidad_reina'         => CalidadReina::class,
            'estado_nutricional'    => EstadoNutricional::class,
            'presencia_varroa'      => PresenciaVarroa::class,
            'presencia_nosemosis'   => PresenciaNosemosis::class,
            'indice_cosecha'        => IndiceCosecha::class,
            'preparacion_invernada' => PreparacionInvernada::class,
        ];

        $valores = [];
        foreach ($pccMap as $key => $model) {
            $registro = $model::where('colmena_id', $colmenaId)
                            ->where('visita_id',  $visita->id)
                            ->first();

            // si existe, lo convierto a array (para rellenar inputs); si no, array vacío
            $valores[$key] = $registro
                ? $registro->toArray()
                : [];
        }

        // 3) Llamo a la misma vista de edición de PCC
        return view('visitas.pcc_edit', compact('visita','colmena','valores'));
    }

    public function updatePcc(Request $request, Visita $visita)
    {
        return $this->storePcc($request, $visita)
            ->with('success', 'PCC actualizado correctamente.');
    }
}
