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
        // Validación de todos los campos que pueden venir del formulario de medicamentos
        $validatedData = $request->validate([
            'fecha' => 'required|date',
            'motivo_tratamiento' => 'required|string', // 'varroa', 'nosema', 'otro'
            'motivo_otro' => 'nullable|string|required_if:motivo_tratamiento,otro', // Requerido si 'otro' está seleccionado
            'responsable' => 'required|string',
            'nombre_comercial_medicamento' => 'required|string',
            'principio_activo_medicamento' => 'required|string',
            'periodo_resguardo' => 'required|string',
            'observaciones' => 'nullable|string',
            // Campos de Varroa (PCC4) - se validan si están presentes, no son estrictamente requeridos por el form si no se selecciona 'varroa'
            'varroa_diagnostico_visual' => 'nullable|string',
            'varroa_muestreo_abejas_adultas' => 'nullable|string',
            'varroa_muestreo_cria_operculada' => 'nullable|string',
            'varroa_metodo_diagnostico' => 'nullable|string',
            'varroa_tratamiento' => 'nullable|string',
            'varroa_fecha_aplicacion' => 'nullable|date',
            'varroa_dosificacion' => 'nullable|string',
            'varroa_metodo_aplicacion' => 'nullable|string',
            'varroa_n_colmenas_tratadas' => 'nullable|integer|min:0',
            'varroa_fecha_monitoreo_varroa' => 'nullable|date',
            'varroa_producto_comercial' => 'nullable|string',
            'varroa_ingrediente_activo' => 'nullable|string',
            'varroa_periodo_carencia' => 'nullable|string',
            // Campos de Nosemosis (PCC5) - se validan si están presentes, no son estrictamente requeridos por el form si no se selecciona 'nosema'
            'nosemosis_signos_clinicos' => 'nullable|string',
            'nosemosis_muestreo_laboratorio' => 'nullable|string',
            'nosemosis_metodo_diagnostico_laboratorio' => 'nullable|string',
            'nosemosis_tratamiento' => 'nullable|string',
            'nosemosis_fecha_aplicacion' => 'nullable|date',
            'nosemosis_dosificacion' => 'nullable|string',
            'nosemosis_metodo_aplicacion' => 'nullable|string',
            'nosemosis_num_colmenas_tratadas' => 'nullable|integer|min:0',
            'nosemosis_fecha_monitoreo_nosema' => 'nullable|date',
            'nosemosis_producto_comercial' => 'nullable|string',
            'nosemosis_ingrediente_activo' => 'nullable|string',
        ]);

        DB::beginTransaction(); // Iniciar transacción para asegurar la integridad de los datos

        try {
            $presenciaVarroaId = null;
            $presenciaNosemosisId = null;

            // 1. Guardar datos específicos de Varroa o Nosemosis si el motivo corresponde
            // Estos registros se crean una sola vez por el tratamiento general del apiario
            if ($validatedData['motivo_tratamiento'] === 'varroa') {
                $presenciaVarroa = PresenciaVarroa::create([
                    'diagnostico_visual' => $validatedData['varroa_diagnostico_visual'] ?? null,
                    'muestreo_abejas_adultas' => $validatedData['varroa_muestreo_abejas_adultas'] ?? null,
                    'muestreo_cria_operculada' => $validatedData['varroa_muestreo_cria_operculada'] ?? null,
                    'metodo_diagnostico' => $validatedData['varroa_metodo_diagnostico'] ?? null,
                    'tratamiento' => $validatedData['varroa_tratamiento'] ?? null,
                    'fecha_aplicacion' => $validatedData['varroa_fecha_aplicacion'] ?? null,
                    'dosificacion' => $validatedData['varroa_dosificacion'] ?? null,
                    'metodo_aplicacion' => $validatedData['varroa_metodo_aplicacion'] ?? null,
                    'n_colmenas_tratadas' => $validatedData['varroa_n_colmenas_tratadas'] ?? null,
                    'fecha_monitoreo_varroa' => $validatedData['varroa_fecha_monitoreo_varroa'] ?? null,
                    'producto_comercial' => $validatedData['varroa_producto_comercial'] ?? null,
                    'ingrediente_activo' => $validatedData['varroa_ingrediente_activo'] ?? null,
                    'periodo_carencia' => $validatedData['varroa_periodo_carencia'] ?? null,
                ]);
                $presenciaVarroaId = $presenciaVarroa->id; // Guardar el ID para vincularlo a las visitas
            } elseif ($validatedData['motivo_tratamiento'] === 'nosema') {
                $presenciaNosemosis = PresenciaNosemosis::create([
                    'signos_clinicos' => $validatedData['nosemosis_signos_clinicos'] ?? null,
                    'muestreo_laboratorio' => $validatedData['nosemosis_muestreo_laboratorio'] ?? null,
                    'metodo_diagnostico_laboratorio' => $validatedData['nosemosis_metodo_diagnostico_laboratorio'] ?? null,
                    'tratamiento' => $validatedData['nosemosis_tratamiento'] ?? null,
                    'fecha_aplicacion' => $validatedData['nosemosis_fecha_aplicacion'] ?? null,
                    'dosificacion' => $validatedData['nosemosis_dosificacion'] ?? null,
                    'metodo_aplicacion' => $validatedData['nosemosis_metodo_aplicacion'] ?? null,
                    'num_colmenas_tratadas' => $validatedData['nosemosis_num_colmenas_tratadas'] ?? null,
                    'fecha_monitoreo_nosema' => $validatedData['nosemosis_fecha_monitoreo_nosema'] ?? null,
                    'producto_comercial' => $validatedData['nosemosis_producto_comercial'] ?? null,
                    'ingrediente_activo' => $validatedData['nosemosis_ingrediente_activo'] ?? null,
                ]);
                $presenciaNosemosisId = $presenciaNosemosis->id; // Guardar el ID para vincularlo a las visitas
            }

            // 2. Obtener todas las colmenas asociadas al apiario actual
            $colmenas = $apiario->colmenas;

            // Determinar el valor final para la columna 'motivo' en la tabla 'visitas'
            $motivoColumnaVisita = ($validatedData['motivo_tratamiento'] === 'otro') ?
                                   ($validatedData['motivo_otro'] ?? 'Otro motivo no especificado') :
                                   $validatedData['motivo_tratamiento'];

            // 3. Crear un registro de Visita para cada colmena del apiario
            foreach ($colmenas as $colmena) {
                Visita::create([
                    'apiario_id' => $apiario->id,
                    'user_id' => auth()->id(), // ID del usuario autenticado
                    'colmena_id' => $colmena->id, // ID de la colmena actual en el bucle
                    'fecha_visita' => $validatedData['fecha'],
                    'num_colmenas_tratadas' => 1, // Asumimos 1 colmena tratada por cada registro de visita individual
                    'motivo_tratamiento' => $validatedData['motivo_tratamiento'], // El motivo seleccionado (varroa, nosema, otro)
                    'motivo' => $motivoColumnaVisita, // El motivo específico para la columna 'motivo'
                    'nombre_comercial_medicamento' => $validatedData['nombre_comercial_medicamento'],
                    'principio_activo_medicamento' => $validatedData['principio_activo_medicamento'],
                    'periodo_resguardo' => $validatedData['periodo_resguardo'],
                    'responsable' => $validatedData['responsable'],
                    'observaciones' => $validatedData['observaciones'] ?? null,
                    'tipo_visita' => 'Uso de Medicamentos',
                    'presencia_varroa_id' => $presenciaVarroaId, // Enlaza el ID de Varroa (o null)
                    'presencia_nosemosis_id' => $presenciaNosemosisId, // Enlaza el ID de Nosemosis (o null)
                ]);

                // Nota: La lógica anterior de "SistemaExperto::create" para PCC4 y PCC5
                // ha sido removida de aquí, ya que los datos se guardan directamente
                // en las tablas `presencia_varroa` y `presencia_nosemosis`.
                // Si `SistemaExperto` tiene un propósito diferente (ej. un resumen posterior),
                // esa lógica debería gestionarse de forma separada.
            }

            DB::commit(); // Confirmar la transacción si todo fue exitoso

            // Redirigir al usuario con un mensaje de éxito
            return redirect()->route('visita.index', $apiario)->with('success', 'Medicamento y evaluaciones guardadas correctamente.');

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción si ocurre un error
            // Registrar el error para depuración
            Log::error('Error al registrar el medicamento en VisitaController: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            // Regresar al formulario con los datos anteriores y un mensaje de error
            return back()->withInput()->with('error', 'Hubo un error al registrar el medicamento. Por favor, intente de nuevo. Detalle: ' . $e->getMessage());
        }
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
