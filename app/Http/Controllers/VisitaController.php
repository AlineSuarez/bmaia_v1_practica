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
    return view('visitas.create1', compact('apiario','visita','user'));
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
            'user_id' => auth()->id(),
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
            'user_id' => auth()->id(),
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
        // Lógica para guardar el registro de visita general (visitas.create1)
        $validated = $request->validate([
            'fecha' => 'required|date',
            'motivo' => 'required|string',
        ]);

        Visita::create([
            'apiario_id' => $apiario->id,
            'user_id' => auth()->id(),
            'fecha_visita' => $validated['fecha'],
            'motivo' => $validated['motivo'],
            'tipo_visita' => 'Visita General',
        ]);
        return redirect()->route('visitas')->with('success', 'Registro de Visita General guardado correctamente.');
    }

    public function storeSistemaExperto(Request $request)
    {
        $request->validate([
            'actividad' => 'required|string',
            'vigor' => 'required|string',
            'presencia_nosemosis.signos_clinicos' => 'required|string',
            'indice_cosecha.madurez_miel' => 'required|string',
            'preparacion_invernada.control_sanitario' => 'required|string',
            // ... agrega más si deseas validar todo
        ]);

        // Guardar PCCs
        $nosemosis = \App\Models\PresenciaNosemosis::create($request->input('presencia_nosemosis'));
        $cosecha = \App\Models\IndiceCosecha::create($request->input('indice_cosecha'));
        $invernada = \App\Models\PreparacionInvernada::create($request->input('preparacion_invernada'));

        // Crear Visita
        $visita = new Visita();
        $visita->user_id = auth()->id();
        $visita->tipo_visita = 'Sistema Experto';
        $visita->fecha_visita = now();
        $visita->actividad_colmena = $request->actividad;
        $visita->vigor_de_colmena = $request->vigor;

        // Asociar FK
        $visita->presencia_nosemosis_id = $nosemosis->id;
        $visita->indice_cosecha_id = $cosecha->id;
        $visita->preparacion_invernada_id = $invernada->id;

        $visita->save();

        return redirect()->route('sistemaexperto.index')->with('success', 'Registro guardado correctamente.');
    }


    /*
    public function storeSistemaExperto(Request $request) {
        $request->validate([
            'actividad' => 'required|string',
            'vigor' => 'required|string',
    
            // PCC1
            'pcc1_vigor_total' => 'nullable|string',
            'pcc1_activity_total' => 'nullable|string',
            'pcc1_pollen_total' => 'nullable|string',
            'pcc1_block_total' => 'nullable|string',
            'pcc1_cells_total' => 'nullable|string',
    
            // PCC2
            'pcc2_postura_total' => 'nullable|string',
            'pcc2_cria_total' => 'nullable|string',
            'pcc2_zanganos_total' => 'nullable|string',
    
            // PCC3
            'pcc3_reserva_total' => 'nullable|string',
    
            // PCC4
            'pcc4_varroa_total' => 'nullable|string',
    
            // PCC5
            'pcc5_nosemosis' => 'nullable|string',
    
            // PCC6
            'pcc6_cosecha_total' => 'nullable|string',
    
            // PCC7
            'pcc7_preparacion_invernada' => 'nullable|string',
        ]);
    
        $visita = new Visita();
        $visita->user_id = auth()->id();
        $visita->tipo_visita = 'Sistema Experto';
        $visita->fecha_visita = now();
    
        // Campos generales
        $visita->actividad = $request->actividad;
        $visita->vigor = $request->vigor;
    
        // PCCs
        $visita->pcc1_vigor_total = $request->pcc1_vigor_total;
        $visita->pcc1_activity_total = $request->pcc1_activity_total;
        $visita->pcc1_pollen_total = $request->pcc1_pollen_total;
        $visita->pcc1_block_total = $request->pcc1_block_total;
        $visita->pcc1_cells_total = $request->pcc1_cells_total;
        $visita->pcc2_postura_total = $request->pcc2_postura_total;
        $visita->pcc2_cria_total = $request->pcc2_cria_total;
        $visita->pcc2_zanganos_total = $request->pcc2_zanganos_total;
        $visita->pcc3_reserva_total = $request->pcc3_reserva_total;
        $visita->pcc4_varroa_total = $request->pcc4_varroa_total;
        $visita->pcc5_nosemosis = $request->pcc5_nosemosis;
        $visita->pcc6_cosecha_total = $request->pcc6_cosecha_total;
        $visita->pcc7_preparacion_invernada = $request->pcc7_preparacion_invernada;
    
        $visita->save();
    
        return redirect()->route('sistemaexperto.index')->with('success', 'Registro guardado correctamente.');
    }

    
    /*
    public function storeSistemaExperto(Request $request, Apiario $apiario){
        $request->validate([
            'fecha_visita' => 'required|date',
            // PCC5
            'pcc5_signos_clinicos' => 'nullable|string',
            'pcc5_muestreo_laboratorio' => 'nullable|string',
            'pcc5_tratamiento' => 'nullable|string',
            'pcc5_fecha_aplicacion' => 'nullable|date',
            'pcc5_dosificacion' => 'nullable|string',
            'pcc5_metodo_aplicacion' => 'nullable|string',
            'pcc5_colmenas_tratadas' => 'nullable|integer',
            // PCC6
            'pcc6_madurez_miel' => 'nullable|string',
            'pcc6_num_alzadas' => 'nullable|string',
            'pcc6_marcos_miel' => 'nullable|string',
            // PCC7
            'pcc7_control_sanitario' => 'nullable|string',
            'pcc7_fusion_colmenas' => 'nullable|string',
            'pcc7_reserva_alimento' => 'nullable|string',
        ]);
    
        $nosemosis = \App\Models\PresenciaNosemosis::create($request->only([
            'pcc5_signos_clinicos',
            'pcc5_muestreo_laboratorio',
            'pcc5_tratamiento',
            'pcc5_fecha_aplicacion',
            'pcc5_dosificacion',
            'pcc5_metodo_aplicacion',
            'pcc5_colmenas_tratadas',
        ]));
    
        $cosecha = \App\Models\IndiceCosecha::create([
            'madurez_miel' => $request->pcc6_madurez_miel,
            'num_alzadas' => $request->pcc6_num_alzadas,
            'marcos_miel' => $request->pcc6_marcos_miel,
        ]);
    
        $invernada = \App\Models\PreparacionInvernada::create([
            'control_sanitario' => $request->pcc7_control_sanitario,
            'fusion_colmenas' => $request->pcc7_fusion_colmenas,
            'reserva_alimento' => $request->pcc7_reserva_alimento,
        ]);
    
        $visita = new Visita();
        $visita->user_id = auth()->id();
        $visita->apiario_id = $apiario->id;
        $visita->tipo_visita = 'Sistema Experto';
        $visita->fecha_visita = $request->fecha_visita;
    
        $visita->presencia_nosemosis_id = $nosemosis->id;
        $visita->indice_cosecha_id = $cosecha->id;
        $visita->preparacion_invernada_id = $invernada->id;
    
        $visita->save();
    
        return redirect()->route('sistemaexperto.index')->with('success', 'Registro del Sistema Experto guardado correctamente.');
    }
         */



    public function showHistorial($apiarioId)
    {
        // Obtener el apiario y sus visitas
        $apiario = Apiario::with('visitas')->findOrFail($apiarioId);
        // Retornar la vista de historial con los datos del apiario y sus visitas
        return view('visitas.historial', compact('apiario'));
    }
}
