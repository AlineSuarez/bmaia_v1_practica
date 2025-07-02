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
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VisitaController extends Controller
{
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
            ->where('id', $id_apiario)
            ->first();

        // Verificar si el apiario existe
        if (!$apiario) {
            abort(404, 'Apiario no encontrado.');
        }
        return view('visitas.create', compact('apiario', 'user'));
    }

    public function createMedicamentos($id_apiario)
    {
        $user = auth()->user();
        $apiario = Apiario::where('user_id', auth()->id())->where('id', $id_apiario)->firstOrFail();
        return view('visitas.create2', compact('apiario', 'user'));
    }

    public function createGeneral($id_apiario)
    {
        $user = auth()->user();
        $apiario = Apiario::where('user_id', auth()->id())->where('id', $id_apiario)->firstOrFail();
        $visita = Visita::where('apiario_id', $id_apiario)->where('tipo_visita', 'Visita General')->first();
        $userFormat = config('app.date_format', 'DD/MM/YYYY');
        return view('visitas.create1', compact('apiario', 'visita', 'user', 'userFormat'));
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
        // Lógica para guardar el registro de inspección de apiario (visitas.create)
        $validated = $request->validate([
            'fecha_inspeccion' => 'required|date',
            'num_colmenas_totales' => 'required|integer',
            'num_colmenas_activas' => 'required|integer',
            'num_colmenas_enfermas' => 'required|integer',
            'num_colmenas_muertas' => 'required|integer',
            'num_colmenas_inspeccionadas' => 'required|integer',
            'flujo_nectar_polen' => 'required|string',
            'nombre_revisor_apiario' => 'required|string',
            'sospecha_enfermedad' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        Visita::create([
            'apiario_id' => $apiario->id,
            //'user_id' => auth()->id(),
            'fecha_visita' => $validated['fecha_inspeccion'],
            'num_colmenas_totales' => $validated['num_colmenas_totales'],
            'num_colmenas_activas' => $validated['num_colmenas_activas'],
            'num_colmenas_enfermas' => $validated['num_colmenas_enfermas'],
            'num_colmenas_muertas' => $validated['num_colmenas_muertas'],
            'num_colmenas_inspeccionadas' => $validated['num_colmenas_inspeccionadas'],
            'flujo_nectar_polen' => $validated['flujo_nectar_polen'],
            'nombre_revisor_apiario' => $validated['nombre_revisor_apiario'],
            'sospecha_enfermedad' => $validated['sospecha_enfermedad'],
            'observaciones' => $validated['observaciones'],
            'tipo_visita' => 'Inspección de Visita',
        ]);
        return redirect()->route('visitas')->with('success', 'Registro de Inspección guardado correctamente.');
    }

    public function storeMedicamentos(Request $request, Apiario $apiario)
    {
        // 1) Validar los campos comunes a todos los motivos
        $commonRules = [
            'fecha' => 'required|date',
            'motivo_tratamiento' => 'required|in:varroa,nosema,otro',
            'motivo_otro' => 'nullable|string|required_if:motivo_tratamiento,otro',
            'responsable' => 'required|string',
            'nombre_comercial_medicamento' => 'required|string',
            'principio_activo_medicamento' => 'required|string',
            'periodo_resguardo' => 'required|string',
            'observaciones' => 'nullable|string',
        ];
        $commonData = $request->validate($commonRules);

        // 2) Preparar reglas condicionales según el motivo
        $pccRules = [];
        if ($commonData['motivo_tratamiento'] === 'varroa') {
            $pccRules = [
                'varroa_metodo_diagnostico' => 'required|string',
                'varroa_diagnostico_visual' => 'nullable|string',
                'varroa_muestreo_abejas_adultas' => 'nullable|string',
                'varroa_muestreo_cria_operculada' => 'nullable|string',
                'varroa_tratamiento' => 'nullable|string',
                'varroa_fecha_aplicacion' => 'nullable|date',
                'varroa_dosificacion' => 'nullable|string',
                'varroa_metodo_aplicacion' => 'nullable|string',
                'varroa_fecha_monitoreo_varroa' => 'nullable|date',
                'varroa_producto_comercial' => 'nullable|string',
                'varroa_ingrediente_activo' => 'nullable|string',
                'varroa_periodo_carencia' => 'nullable|integer',
            ];
        } elseif ($commonData['motivo_tratamiento'] === 'nosema') {
            $pccRules = [
                'nosemosis_metodo_diagnostico_laboratorio' => 'required|string',
                'nosemosis_signos_clinicos' => 'nullable|string',
                'nosemosis_muestreo_laboratorio' => 'nullable|string',
                'nosemosis_tratamiento' => 'nullable|string',
                'nosemosis_fecha_aplicacion' => 'nullable|date',
                'nosemosis_dosificacion' => 'nullable|string',
                'nosemosis_metodo_aplicacion' => 'nullable|string',
                'nosemosis_fecha_monitoreo_nosema' => 'nullable|date',
                'nosemosis_producto_comercial' => 'nullable|string',
                'nosemosis_ingrediente_activo' => 'nullable|string',
            ];
        }
        // Si es "otro", $pccRules queda vacío y no validamos nada más.

        $pccData = $pccRules
            ? $request->validate($pccRules)
            : [];

        // 3) Unificar todos los datos validados
        $data = array_merge($commonData, $pccData);

        // 4) Guardar en transacción
        DB::transaction(function () use ($data, $apiario) {
            // 4.1) Creamos la Visita única
            $visita = Visita::create([
                'apiario_id' => $apiario->id,
                'user_id' => auth()->id(),
                'fecha_visita' => $data['fecha'],
                'tipo_visita' => 'Uso de Medicamentos',
                'motivo_tratamiento' => $data['motivo_tratamiento'],
                'motivo' => $data['motivo_tratamiento'] === 'otro'
                    ? $data['motivo_otro']
                    : $data['motivo_tratamiento'],
                'nombre_comercial_medicamento' => $data['nombre_comercial_medicamento'],
                'principio_activo_medicamento' => $data['principio_activo_medicamento'],
                'periodo_resguardo' => $data['periodo_resguardo'],
                'responsable' => $data['responsable'],
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            // 4.2) Por cada colmena, creamos el PCC correspondiente
            if ($data['motivo_tratamiento'] === 'varroa') {
                $firstId = null;
                foreach ($apiario->colmenas as $colmena) {
                    $pv = PresenciaVarroa::create([
                        'colmena_id' => $colmena->id,
                        'visita_id' => $visita->id,
                        'metodo_diagnostico' => $data['varroa_metodo_diagnostico'],
                        'diagnostico_visual' => $data['varroa_diagnostico_visual'] ?? null,
                        'muestreo_abejas_adultas' => $data['varroa_muestreo_abejas_adultas'] ?? null,
                        'muestreo_cria_operculada' => $data['varroa_muestreo_cria_operculada'] ?? null,
                        'tratamiento' => $data['varroa_tratamiento'] ?? null,
                        'fecha_aplicacion' => $data['varroa_fecha_aplicacion'] ?? null,
                        'dosificacion' => $data['varroa_dosificacion'] ?? null,
                        'metodo_aplicacion' => $data['varroa_metodo_aplicacion'] ?? null,
                        'fecha_monitoreo_varroa' => $data['varroa_fecha_monitoreo_varroa'] ?? null,
                        'producto_comercial' => $data['varroa_producto_comercial'] ?? null,
                        'ingrediente_activo' => $data['varroa_ingrediente_activo'] ?? null,
                        'periodo_carencia' => $data['varroa_periodo_carencia'] ?? null,
                        'n_colmenas_tratadas' => 1,
                    ]);
                    $firstId = $firstId ?? $pv->id;
                }
                $visita->update(['presencia_varroa_id' => $firstId]);
            } elseif ($data['motivo_tratamiento'] === 'nosema') {
                $firstId = null;
                foreach ($apiario->colmenas as $colmena) {
                    $pn = PresenciaNosemosis::create([
                        'colmena_id' => $colmena->id,
                        'visita_id' => $visita->id,
                        'metodo_diagnostico_laboratorio' => $data['nosemosis_metodo_diagnostico_laboratorio'],
                        'signos_clinicos' => $data['nosemosis_signos_clinicos'] ?? null,
                        'muestreo_laboratorio' => $data['nosemosis_muestreo_laboratorio'] ?? null,
                        'tratamiento' => $data['nosemosis_tratamiento'] ?? null,
                        'fecha_aplicacion' => $data['nosemosis_fecha_aplicacion'] ?? null,
                        'dosificacion' => $data['nosemosis_dosificacion'] ?? null,
                        'metodo_aplicacion' => $data['nosemosis_metodo_aplicacion'] ?? null,
                        'fecha_monitoreo_nosema' => $data['nosemosis_fecha_monitoreo_nosema'] ?? null,
                        'producto_comercial' => $data['nosemosis_producto_comercial'] ?? null,
                        'ingrediente_activo' => $data['nosemosis_ingrediente_activo'] ?? null,
                        'num_colmenas_tratadas' => 1,
                    ]);
                    $firstId = $firstId ?? $pn->id;
                }
                $visita->update(['presencia_nosemosis_id' => $firstId]);
            }
            // si es “otro” no creamos PCC adicional
        });

        return redirect()
            ->route('visitas.historial', $apiario)
            ->with('success', 'Registro de uso de medicamentos guardado correctamente.');
    }


    public function storeGeneral(Request $request, Apiario $apiario)
    {
        // 1) Validación básica: 'fecha' debe venir como date (YYYY-MM-DD), 'motivo' como texto.
        $validated = $request->validate([
            'fecha' => 'required|date',
            'motivo' => 'required|string',
        ]);

        // 2) Como el input es "date", $validated['fecha'] ya es "YYYY-MM-DD", 
        //    no hace falta hacer createFromFormat. 
        //    Simplemente lo guardamos tal cual.
        Visita::create([
            'apiario_id' => $apiario->id,
            'fecha_visita' => $validated['fecha'],
            'motivo' => $validated['motivo'],
            'tipo_visita' => 'Visita General',
            'nombres' => $request->user()->name,
            'apellidos' => $request->user()->last_name,
            'rut' => $request->user()->rut,
            'telefono' => $request->user()->telefono,
            'firma' => $request->user()->firma,
        ]);
        return redirect()->route('visitas.historial', $apiario)->with('success', 'Registro de Visita General guardado correctamente.');
    }

    public function storeAlimentacion(Request $request, Apiario $apiario)
    {
        // 1) Validación de inputs
        $data = $request->validate([
            'objetivo' => 'required|in:estimulacion,mantencion',
            'tipo_alimentacion' => 'required|string|max:255',
            'fecha_aplicacion_insumo_utilizado' => 'required|date',
            'insumo_utilizado' => 'nullable|string|max:255',
            'dosificacion' => 'nullable|string|max:255',
            'metodo_utilizado' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($data, $apiario) {
            // 2) Creamos **UNA SOLA** Visita de Alimentación
            $visita = Visita::create([
                'apiario_id' => $apiario->id,
                'user_id' => auth()->id(),
                'tipo_visita' => 'Alimentación',
                'fecha_visita' => now(),
            ]);
            $firstId = null;
            // 3) Por cada colmena, creamos el estado nutricional vinculado a esa visita
            foreach ($apiario->colmenas as $colmena) {
                $en = EstadoNutricional::create([
                    'colmena_id' => $colmena->id,
                    'visita_id' => $visita->id,
                    'objetivo' => $data['objetivo'],
                    'tipo_alimentacion' => $data['tipo_alimentacion'],
                    'fecha_aplicacion' => $data['fecha_aplicacion_insumo_utilizado'],
                    'insumo_utilizado' => $data['insumo_utilizado'],
                    'dosifiacion' => $data['dosificacion'],
                    'metodo_utilizado' => $data['metodo_utilizado'],
                    'n_colmenas_tratadas' => 1,
                ]);
                $firstId = $firstId ?? $en->id;
            }
            $visita->update(['estado_nutricional_id' => $firstId]);
        });

        // 4) Redirigimos al historial del apiario
        return redirect()
            ->route('visitas.historial', $apiario)
            ->with('success', 'Registros de alimentación guardados correctamente.');
    }

    public function showHistorial($apiarioId)
    {
        // Obtener el apiario y sus visitas
        $apiario = Apiario::with('visitas.usuario')->findOrFail($apiarioId);
        // Retornar la vista de historial con los datos del apiario y sus visitas
        return view('visitas.historial', compact('apiario'));
    }
}