<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\{
    Apiario,
    Visita,
    EstadoNutricional,
    SistemaExperto,
};

class VisitaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;

        // 1) Fijos
        $apiariosFijos = Apiario::with('comuna')
            ->where('user_id', $userId)
            ->where('tipo_apiario', 'fijo')
            ->get();

        // 2) Trashumantes “base” (NO temporales)
        $apiariosBase = Apiario::where('user_id', $userId)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->where('es_temporal', false)
            ->get();

        // 3) Apiarios temporales (los que creaste con el wizard)
        $apiariosTemporales = Apiario::where('user_id', $userId)
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
        // 1) Validar
        $data = $request->validate([
            'objetivo' => 'required|in:estimulacion,mantencion',
            'tipo_alimentacion' => 'required|string|max:255',
            'fecha_aplicacion_insumo_utilizado' => 'required|date',
            'insumo_utilizado' => 'nullable|string|max:255',
            'dosificacion' => 'nullable|string|max:255',
            'metodo_utilizado' => 'required|string|max:255',
        ]);

        // 2) Guardar en estado_nutricional
        $nutricional = EstadoNutricional::create([
            'objetivo' => $data['objetivo'],
            'tipo_alimentacion' => $data['tipo_alimentacion'],
            'fecha_aplicacion' => $data['fecha_aplicacion_insumo_utilizado'],
            'insumo_utilizado' => $data['insumo_utilizado'],
            'dosifiacion' => $data['dosificacion'],
            'metodo_utilizado' => $data['metodo_utilizado'],
        ]);

        // 3) Elegir la colmena sobre la que actuamos
        $colmena = $apiario->colmenas()->first();
        if (!$colmena) {
            return back()->withErrors('Este apiario no tiene colmenas.');
        }

        // 4) Crear la Visita y asociar estado_nutricional + colmena
        $visita = Visita::create([
            'apiario_id' => $apiario->id,
            'user_id' => auth()->id(),
            'tipo_visita' => 'Alimentación',
            'fecha_visita' => now(),
            'estado_nutricional_id' => $nutricional->id,
        ]);

        // 5) Sincronizar PCC3 en SistemaExperto
        SistemaExperto::updateOrCreate(
            ['colmena_id' => $colmena->id],
            [
                'apiario_id' => $apiario->id,
                'pcc3_objetivo' => $nutricional->objetivo,
                'pcc3_tipo_alimentacion' => $nutricional->tipo_alimentacion,
                'pcc3_fecha_aplicacion' => $nutricional->fecha_aplicacion,
                'pcc3_insumo_utilizado' => $nutricional->insumo_utilizado,
                'pcc3_dosificacion' => $nutricional->dosifiacion,
                'pcc3_metodo_utilizado' => $nutricional->metodo_utilizado,
            ]
        );

        // 5) Redirigir de vuelta al wizard o donde prefieras
        return redirect()
            ->route('visitas', $apiario->id)
            ->with('success', 'Alimentación guardada y PCC3 sincronizado.');
    }

    public function showHistorial($apiarioId)
    {
        // Obtener el apiario y sus visitas
        $apiario = Apiario::with('visitas.usuario')->findOrFail($apiarioId);
        // Retornar la vista de historial con los datos del apiario y sus visitas
        return view('visitas.historial', compact('apiario'));
    }
}
