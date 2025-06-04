<?php
namespace App\Http\Controllers;

use App\Models\Apiario;
use App\Models\Visita;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

class VisitaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $apiarios = Apiario::where('user_id', auth()->id())->get();
        return view('visitas.index', compact('apiarios','user'));
    }

    public function show(Apiario $apiario)
    {
        $visitas = $apiario->visitas()->latest()->get();
        return view('visitas.show', compact('apiario', 'visitas'));
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
    return view('visitas.create', compact('apiario','user'));
    }

    public function createMedicamentos($id_apiario)
    {
        $user = auth()->user();
        $apiario = Apiario::where('user_id', auth()->id())->where('id', $id_apiario)->firstOrFail();
    return view('visitas.create2', compact('apiario','user'));
    }

    public function createGeneral($id_apiario)
    {
        $user = auth()->user();
        $apiario = Apiario::where('user_id', auth()->id())->where('id', $id_apiario)->firstOrFail();
        $visita = Visita::where('apiario_id', $id_apiario)->where('tipo_visita', 'Visita General')->first();
        $userFormat = config('app.date_format', 'DD/MM/YYYY');
        return view('visitas.create1', compact('apiario','visita','user','userFormat'));
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
        // Lógica para guardar el registro de uso de medicamentos (visitas.create2)
        $validated = $request->validate([
            'fecha' => 'required|date',
            'num_colmenas_tratadas' => 'required|integer',
            'motivo_tratamiento' => 'required|string',
            'nombre_comercial_medicamento' => 'required|string',
            'principio_activo_medicamento' => 'required|string',
            'periodo_resguardo' => 'required|string',
            'responsable' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        Visita::create([
            'apiario_id' => $apiario->id,
            //'user_id' => auth()->id(),
            'fecha_visita' => $validated['fecha'],
            'num_colmenas_tratadas' => $validated['num_colmenas_tratadas'],
            'motivo_tratamiento' => $validated['motivo_tratamiento'],
            'nombre_comercial_medicamento' => $validated['nombre_comercial_medicamento'],
            'principio_activo_medicamento' => $validated['principio_activo_medicamento'],
            'periodo_resguardo' => $validated['periodo_resguardo'],
            'responsable' => $validated['responsable'],
            'observaciones' => $validated['observaciones'],
            'tipo_visita' => 'Uso de Medicamentos',
        ]);
        return redirect()->route('visitas')->with('success', 'Registro de Uso de Medicamentos guardado correctamente.');
    }

    public function storeGeneral(Request $request, Apiario $apiario)
    {
        // 1) Validación básica: 'fecha' debe venir como date (YYYY-MM-DD), 'motivo' como texto.
        $validated = $request->validate([
            'fecha'  => 'required|date', 
            'motivo' => 'required|string',
        ]);

        // 2) Como el input es "date", $validated['fecha'] ya es "YYYY-MM-DD", 
        //    no hace falta hacer createFromFormat. 
        //    Simplemente lo guardamos tal cual.
        Visita::create([
            'apiario_id'   => $apiario->id,
            'fecha_visita' => $validated['fecha'],
            'motivo'       => $validated['motivo'],
            'tipo_visita'  => 'Visita General',
            'nombres'      => $request->user()->name,
            'apellidos'    => $request->user()->last_name,
            'rut'          => $request->user()->rut,
            'telefono'     => $request->user()->telefono,
            'firma'        => $request->user()->firma,
        ]);
        return redirect()->route('visitas.historial', $apiario)->with('success', 'Registro de Visita General guardado correctamente.');
    }

    public function storeSistemaExperto(Request $request)
{
    // 1. Validaciones
    $request->validate([
        // PCC1 - Desarrollo de la Cámara de Cría
        'desarrollo_cria.vigor_colmena' => 'nullable|string',
        'desarrollo_cria.actividad_abejas' => 'nullable|string',
        'desarrollo_cria.ingreso_polen' => 'nullable|string',
        'desarrollo_cria.bloqueo_camara_cria' => 'nullable|string',
        'desarrollo_cria.presencia_celdas_reales' => 'nullable|string',
        // PCC2 - Calidad de la Reina
        'calidad_reina.postura_reina' => 'nullable|string',
        'calidad_reina.estado_cria' => 'nullable|string',
        'calidad_reina.postura_zanganos' => 'nullable|string',
        // PCC3 - Estado Nutricional
        'estado_nutricional.reserva_miel_polen' => 'nullable|string',
        'estado_nutricional.tipo_alimentacion' => 'nullable|string',
        'estado_nutricional.fecha_aplicacion' => 'nullable|date',
        'estado_nutricional.insumo_utilizado' => 'nullable|string',
        'estado_nutricional.dosifiacion' => 'nullable|string',
        'estado_nutricional.metodo_utilizado' => 'nullable|string',
        'estado_nutricional.n_colmenas_tratadas' => 'nullable|integer',
        // PCC4 - Varroa
        'presencia_varroa.diagnostico_visual' => 'nullable|string',
        'presencia_varroa.muestreo_abejas_adultas' => 'nullable|string',
        'presencia_varroa.muestreo_cria_operculada' => 'nullable|string',
        'presencia_varroa.tratamiento' => 'nullable|string',
        'presencia_varroa.fecha_aplicacion' => 'nullable|date',
        'presencia_varroa.dosificacion' => 'nullable|string',
        'presencia_varroa.metodo_aplicacion' => 'nullable|string',
        'presencia_varroa.n_colmenas_tratadas' => 'nullable|integer',
        // PCC5 - Nosemosis
        'presencia_nosemosis.signos_clinicos' => 'nullable|string',
        'presencia_nosemosis.muestreo_laboratorio' => 'nullable|string',
        'presencia_nosemosis.tratamiento' => 'nullable|string',
        'presencia_nosemosis.fecha_aplicacion' => 'nullable|date',
        'presencia_nosemosis.dosificacion' => 'nullable|string',
        'presencia_nosemosis.metodo_aplicacion' => 'nullable|string',
        'presencia_nosemosis.num_colmenas_tratadas' => 'nullable|integer',
        // PCC6 - Índice de Cosecha
        'indice_cosecha.madurez_miel' => 'nullable|string',
        'indice_cosecha.num_alzadas' => 'nullable|numeric',
        'indice_cosecha.marcos_miel' => 'nullable|numeric',
        // PCC7 - Preparación Invernada
        'preparacion_invernada.control_sanitario' => 'nullable|string',
        'preparacion_invernada.fusion_colmenas' => 'nullable|string',
        'preparacion_invernada.reserva_alimento' => 'nullable|string',
    ]);

    // 2. Guardar cada PCC en su tabla y obtener ID
    $desarrolloCria = \App\Models\DesarrolloCria::create($request->input('desarrollo_cria', []));
    $calidadReina = \App\Models\CalidadReina::create($request->input('calidad_reina', []));
    $estadoNutricional = \App\Models\EstadoNutricional::create($request->input('estado_nutricional', []));
    $presenciaVarroa = \App\Models\PresenciaVarroa::create($request->input('presencia_varroa', []));
    $presenciaNosemosis = \App\Models\PresenciaNosemosis::create($request->input('presencia_nosemosis', []));
    $indiceCosecha = \App\Models\IndiceCosecha::create($request->input('indice_cosecha', []));
    $preparacionInvernada = \App\Models\PreparacionInvernada::create($request->input('preparacion_invernada', []));

    // 3. Crear la visita principal y guardar los ID de cada PCC
    $visita = \App\Models\Visita::create([
        //'user_id' => auth()->id(),
        'apiario_id' => $request->apiario_id,
        'fecha_visita' => now(),
        'tipo_visita' => 'Sistema Experto',
        'desarrollo_cria_id' => $desarrolloCria->id,
        'calidad_reina_id' => $calidadReina->id,
        'estado_nutricional_id' => $estadoNutricional->id,
        'presencia_varroa_id' => $presenciaVarroa->id,
        'presencia_nosemosis_id' => $presenciaNosemosis->id,
        'indice_cosecha_id' => $indiceCosecha->id,
        'preparacion_invernada_id' => $preparacionInvernada->id,
    ]);

    return redirect()->route('sistemaexperto')->with('success', 'Registro guardado correctamente.');
}

    public function showHistorial($apiarioId)
    {
        // Obtener el apiario y sus visitas
        $apiario = Apiario::with('visitas.usuario')->findOrFail($apiarioId);
        // Retornar la vista de historial con los datos del apiario y sus visitas
        return view('visitas.historial', compact('apiario'));
    }
}
