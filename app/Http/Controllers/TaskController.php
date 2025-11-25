<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TareaGeneral;
use App\Models\SubTarea;
use App\Models\TareasPredefinidas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// Controlador para gestionar las tareas y subtareas
class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Si no existen subtareas para el usuario, insertar tareas predefinidas automáticamente
        if (SubTarea::where('user_id', $user->id)->count() === 0) {
            $predefinidas = TareasPredefinidas::all();
            // Crear subtareas basadas en las tareas predefinidas
            foreach ($predefinidas as $tarea) {
                $prioridad = $this->normalizePriority($tarea->prioridad ?? 'media'); // Normalizar prioridad
                SubTarea::create([
                    'tarea_general_id' => $tarea->tarea_general_id,
                    'user_id' => $user->id,
                    'nombre' => $tarea->nombre,
                    'fecha_inicio' => $tarea->fecha_inicio ?? now(),
                    'fecha_limite' => $tarea->fecha_limite ?? now()->addDays(7),
                    'prioridad' => $prioridad,
                    'prioridad_base' => $prioridad, // Guardar también como prioridad base
                    'estado' => 'Pendiente',
                    'archivada' => false,
                ]);
            }
        }

        // Mostrar solo tareas NO archivadas
        $subtareas = SubTarea::where('user_id', $user->id)
            ->where('archivada', false)
            ->get()
            ->filter();

        $tareasGeneralesIds = $subtareas->pluck('tarea_general_id')->unique();

        $listaEtapa = TareaGeneral::with('predefinidas')
            ->has('predefinidas')
            ->get();

        $tareasGenerales = TareaGeneral::whereIn('id', $tareasGeneralesIds)
            ->with([
                'subtareas' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('archivada', false);
                }
            ])
            ->get();

        $apiarios = $user->apiarios;

        $tareasGenerales->each(function ($tarea) {
            if ($tarea->fecha_inicio) {
                $tarea->fecha_inicio_formatted = Carbon::parse($tarea->fecha_inicio)->format('d-m-Y');
            }
            if ($tarea->fecha_fin) {
                $tarea->fecha_fin_formatted = Carbon::parse($tarea->fecha_fin)->format('d-m-Y');
            }
            $tarea->subtareas->each(function ($subtarea) {
                if ($subtarea->fecha_inicio) {
                    $subtarea->fecha_inicio_formatted = Carbon::parse($subtarea->fecha_inicio)->format('d-m-Y');
                }
                if ($subtarea->fecha_limite) {
                    $subtarea->fecha_fin_formatted = Carbon::parse($subtarea->fecha_limite)->format('d-m-Y');
                }
            });
        });

        return view('tareas.index', compact('subtareas', 'tareasGenerales', 'apiarios', 'listaEtapa'));
    }

    public function archivar($id)
    {
        $tarea = SubTarea::findOrFail($id);
        $tarea->archivada = true;
        $tarea->save();

        return redirect()->route('tareas')->with('success', 'Tarea archivada correctamente.');
    }

    public function restaurar($id)
    {
        $tarea = SubTarea::findOrFail($id);
        $tarea->archivada = false;
        $tarea->save();

        return redirect()->route('tareas')->with('success', 'Tarea restaurada correctamente.');
    }

    public function verArchivadas()
    {
        $user = Auth::user();

        $tareasArchivadas = SubTarea::where('user_id', $user->id)
                                ->where('archivada', true)
                                ->get();

        return view('tareas.archivadas', compact('tareasArchivadas'));
    }

    public function loadView($view)
    {
        $user = Auth::user();
        // Obtener tareas con subtareas relacionadas
        $tareasGenerales = TareaGeneral::where('user_id', $user->id)
            ->with('subtareas') // Cargar subtareas relacionadas
            ->get();
        // Obtener subtareas de todas las TareasGenerales
        $subtareas = $tareasGenerales->flatMap->subtareas; // Extraer subtareas en una sola colección
        // Formatear fechas en TareasGenerales y Subtareas
        $tareasGenerales->each(function ($tarea) {
            if ($tarea->fecha_inicio) {
                $tarea->fecha_inicio_formatted = Carbon::parse($tarea->fecha_inicio)->format('d-m-Y');
            }
            if ($tarea->fecha_fin) {
                $tarea->fecha_fin_formatted = Carbon::parse($tarea->fecha_fin)->format('d-m-Y');
            }
            // Formatear fechas en cada subtarea
            $tarea->subtareas->each(function ($subtarea) {
                if ($subtarea->fecha_inicio) {
                    $subtarea->fecha_inicio_formatted = Carbon::parse($subtarea->fecha_inicio)->format('d-m-Y');
                }
                if ($subtarea->fecha_limite) {
                    $subtarea->fecha_fin_formatted = Carbon::parse($subtarea->fecha_fin)->format('d-m-Y');
                }
            });
        });
        // Verificar si la vista existe y renderizarla
        if (view()->exists("tareas.{$view}")) {
            return view("tareas.{$view}", compact('tareasGenerales', 'subtareas'))->render();
        }
        return abort(404);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);
        // Obtener la tarea general por su ID
        $tareaGeneral = TareaGeneral::with('subtareas')->findOrFail($id);
        // También puedes cargar todas las tareas generales si es necesario
        $tareasGenerales = TareaGeneral::with('subtareas')->get();
        // Pasar las tareas generales a la vista
        return view('tareas.show', compact('tareaGeneral', 'tareasGenerales', 'task'));
    }

    public function updateTarea(Request $request, $id)
    {
        try {
            $subtarea = Subtarea::findOrFail($id);
            $subtarea->update([
                'fecha_inicio' => $request->input('fecha_inicio'),
                'fecha_limite' => $request->input('fecha_limite'),
                'prioridad' => $request->input('prioridad'),
                'estado' => $request->input('estado'),
            ]);
            if ($request->ajax()) {
                return response()->json(['message' => 'Subtarea actualizada con éxito.']);
            }
            return redirect()->back()->with('success', 'Subtarea actualizada correctamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar tarea: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['message' => 'Error interno'], 500);
            }
            return redirect()->back()->with('error', 'Error al actualizar tarea.');
        }
    }

    // Normaliza la prioridad a los valores esperados en español
    protected function normalizePriority(?string $p): string
    {
        if (!$p) return 'baja';
        $p = strtolower(trim($p));
        $map = [
            'low' => 'baja', 'baja' => 'baja',
            'medium' => 'media', 'media' => 'media',
            'high' => 'alta', 'alta' => 'alta',
            'urgent' => 'urgente', 'urgente' => 'urgente',
        ];
        return $map[$p] ?? 'baja';
    }

    // Actualiza una subtarea existente
    public function update(Request $request, SubTarea $subtarea)
    {   // Registrar los datos recibidos para depuración
        logger()->info('Datos recibidos en update:', $request->all());
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'prioridad' => 'nullable|string',
            'estado' => 'required|in:Pendiente,En progreso,Completada,Vencida',
        ]);

        // Normalizar prioridad
        $prioridad = $this->normalizePriority($request->prioridad);

        // Actualizar subtarea con los datos validados
        $subtarea->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_limite' => $request->fecha_fin,
            'prioridad' => $prioridad,  // Usar la prioridad normalizada
            'estado' => $request->estado,
        ]);
        // Redirigir con mensaje de éxito
        return redirect()->route('tareas')->with('success', 'Subtarea actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        // Buscar subtarea por ID y verificar que pertenece a una tarea general del usuario
        $subtarea = Subtarea::where('user_id', $user->id)->findOrFail($id);
        $subtarea->delete();
        /* return redirect()->route('tareas')->with('success', 'Subtarea eliminada exitosamente.'); */
        return redirect()->route('tareas')->with('success', 'Subtarea eliminada exitosamente.');
    }

    public function guardarCambios(Request $request, $id)
    {
        // Permitir también actualizar el nombre; validaciones opcionales
        $request->validate([
            'nombre' => 'nullable|string|max:255',  // Nuevo campo nombre
            'estado' => 'nullable|in:Pendiente,En progreso,Completada,Vencida',
            'fecha_inicio' => 'nullable|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_inicio',
            'prioridad' => 'nullable|string',
        ]);

        // Encontrar la subtarea por ID
        $subtarea = SubTarea::findOrFail($id);

        // Log para debugging
        Log::info('Guardando cambios en subtarea', [
            'id' => $id,
            'nombre_anterior' => $subtarea->nombre,
            'nombre_nuevo' => $request->input('nombre'),
            'datos_request' => $request->all()
        ]);

        // Construir array de datos para actualizar
        $data = [];
        
        // Solo agregar campos que están presentes en la petición y no son nulos
        if ($request->has('nombre') && !is_null($request->input('nombre'))) {
            $data['nombre'] = $request->input('nombre');
        }
        if ($request->has('estado') && !is_null($request->input('estado'))) {
            $data['estado'] = $request->input('estado');
        }
        if ($request->has('fecha_inicio') && !is_null($request->input('fecha_inicio'))) {
            $data['fecha_inicio'] = $request->input('fecha_inicio');
        }
        if ($request->has('fecha_limite') && !is_null($request->input('fecha_limite'))) {
            $data['fecha_limite'] = $request->input('fecha_limite');
        }
        if ($request->has('prioridad') && !is_null($request->input('prioridad'))) {
            $data['prioridad'] = $this->normalizePriority($request->input('prioridad'));
        }

        $subtarea->update($data);
        $subtarea->refresh(); // Refrescar para obtener los datos actualizados

        Log::info('Subtarea actualizada', [
            'id' => $id,
            'nombre_final' => $subtarea->nombre,
            'datos_guardados' => $data
        ]);

        if ($request->ajax()) {
            // devolver la subtarea actualizada para que el cliente use el nombre persistido
            return response()->json([
                'message' => 'Tarea modificada con éxito.',
                'tarea' => $subtarea->toArray()
            ]);
        }
        return redirect()->route('tareas')->with('success', 'Tarea modificada con éxito');
    }


    // Actualiza solo el estado de una subtarea
    public function updateStatus(Request $request, $id)
    {
        Log::info('Entró a updateStatus', [
            'id' => $id,
            'estado' => $request->estado
        ]);
        $request->validate([
            'estado' => 'required|in:Pendiente,En progreso,Completada,Vencida',
        ]);
        $subtarea = SubTarea::findOrFail($id); // Buscar la subtarea por su ID
        $subtarea->update([
            'estado' => $request->estado,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente'
        ]);
        // return redirect()->route('tareas')->with('success', 'Estado de la subtarea actualizado.');
    }

    // Función para la vista de calendario
    public function calendario()
    {
        return view('tareas.calendario');
    }

    public function obtenerEventosJson()
    {
        // Trae las subtareas del usuario (o Task si prefieres)
        $subtareas = SubTarea::where('user_id', Auth::id())->get();
        // Mapea a lo que FullCalendar espera
        $events = $subtareas->map(function ($st) {
            return [
                'id' => $st->id,
                'title' => $st->nombre,
                // FullCalendar trata `end` como exclusivo, así que sumamos un día
                'start' => Carbon::parse($st->fecha_inicio)->toDateString(),
                'end' => Carbon::parse($st->fecha_limite)->addDay()->toDateString(),
                // Todo lo extra va en extendedProps
                'extendedProps' => [
                    'estado' => $st->estado,
                    'prioridad' => $st->prioridad,
                    'description' => $st->descripcion,
                ],
            ];
        });
        return response()->json($events);
    }

    /**
     * Actualiza en bloque las subtareas recibidas: guarda nuevas fechas y estado.
     * Estructura esperada: { tasks: [ { id, fecha_inicio, fecha_limite, estado }, ... ] }
     */
    public function actualizarPlanTrabajo(Request $request)
    {
        $user = Auth::user();

        $data = $request->all();
        $tasks = data_get($data, 'tasks', []);

        if (!is_array($tasks) || count($tasks) === 0) {
            return response()->json(['error' => 'No se recibieron tareas para actualizar.'], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($tasks as $t) {
                $id = data_get($t, 'id');
                $fecha_inicio = data_get($t, 'fecha_inicio');
                $fecha_limite = data_get($t, 'fecha_limite');
                $estado = data_get($t, 'estado', 'Pendiente');

                if (!$id) continue;

                $subtarea = SubTarea::find($id);
                // Solo actualizar si existe y pertenece al usuario autenticado
                if (!$subtarea || $subtarea->user_id !== $user->id) continue;

                $subtarea->fecha_inicio = $fecha_inicio;
                $subtarea->fecha_limite = $fecha_limite;
                $subtarea->estado = $estado;
                $subtarea->save();
            }

            DB::commit();
            return response()->json(['ok' => true, 'message' => 'Plan de Trabajo actualizado correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar plan de trabajo: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Error interno al actualizar tareas.'], 500);
        }
    }

    public function default(Request $request)
    {
        // Validar que se reciban las subtareas seleccionadas
        $request->validate([
            'subtareas' => 'required|array',
            'subtareas.*' => 'exists:tareas_predefinidas,id',
        ]);
        // Obtener el usuario autenticado
        $user = Auth::user();
        // Iterar sobre las subtareas seleccionadas
        foreach ($request->subtareas as $subtareaId) {
            // Buscar la tarea predefinida
            $tareaPredefinida = TareasPredefinidas::findOrFail($subtareaId);
            // Ajustar fechas
            $fechaInicio = Carbon::parse($tareaPredefinida->fecha_inicio);
            $fechaLimite = Carbon::parse($tareaPredefinida->fecha_limite);
            $hoy = Carbon::now();
            if ($fechaInicio->isPast() && $fechaInicio->isSameDay($hoy)) {
                $fechaInicio->addYear();
            }
            if ($fechaLimite->isPast() && $fechaLimite->isSameDay($hoy)) {
                $fechaLimite->addYear();
            }
            $prioridad = $this->normalizePriority($tareaPredefinida->prioridad ?? 'baja');
            // Crear una nueva subtarea asociada al usuario y a la misma tarea general
            Subtarea::create([
                'tarea_general_id' => $tareaPredefinida->tarea_general_id,
                'user_id' => $user->id,
                'nombre' => $tareaPredefinida->nombre,
                'fecha_inicio' => $fechaInicio,
                'fecha_limite' => $fechaLimite,
                'prioridad' => $prioridad,
                'prioridad_base' => $prioridad, // Guardar también como prioridad base
            ]);
        }
        // Redireccionar con un mensaje de éxito
        return redirect()->route('tareas')->with('success', 'Las tareas seleccionadas se han agregado correctamente a tu tablero.');
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'tarea_general_id' => 'required',
            'subtareas' => 'required|array',
            'subtareas.*.nombre' => 'required|string|max:255',
            'subtareas.*.fecha_inicio' => 'required|date',
            'subtareas.*.fecha_fin' => 'required|date',
        ]);
        // Validación personalizada de fechas
        foreach ($request->subtareas as $subtarea) {
            if (strtotime($subtarea['fecha_fin']) < strtotime($subtarea['fecha_inicio'])) {
                return redirect()->back()
                    ->withErrors(['subtareas' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio en todas las subtareas.'])
                    ->withInput();
            }
        }
        try {
            // Crear la Tarea General
            $tareaGeneral = TareaGeneral::findOrFail($request->tarea_general_id);
            // Crear Subtareas asociadas
            foreach ($request->subtareas as $subtarea) {
                $prioridad = $this->normalizePriority($subtarea['prioridad'] ?? 'baja');
                $tareaGeneral->subtareas()->create([
                    'nombre' => $subtarea['nombre'],
                    'fecha_inicio' => $subtarea['fecha_inicio'],
                    'fecha_limite' => $subtarea['fecha_fin'],
                    'estado' => $subtarea['estado'],
                    'prioridad' => $prioridad,
                    'prioridad_base' => $prioridad, // Guardar también como prioridad base
                    'user_id' => Auth::id(),
                ]);
            }
            // En caso de éxito
            return redirect()->route('tareas')->with('success', 'Tareas creadas exitosamente.');
        } catch (\Exception $e) {
            // Registrar el error si es necesario
            //Log::error('Error al crear la Tarea General o Subtareas: ' . $e->getMessage());
            // En caso de error
            return redirect()->back()
                ->with('error', 'Ocurrió un error al crear las tareas. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        if (!$query) {
            return response()->json([], 200);
        }

        $tareas = TareaGeneral::where('nombre', 'LIKE', '%' . $query . '%')
            ->where('user_id', Auth::id()) // Asegúrate de filtrar por usuario autenticado
            ->get(['id', 'nombre']); // Selecciona solo los campos necesarios

        return response()->json($tareas);
    }

    public function storeAjax(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255'
        ]);

        $etapa = TareaGeneral::create([
            'nombre' => $request->nombre,
        ]);

        return response()->json($etapa);
    }

    public function obtenerSubtareasJson()
    {
        $user = Auth::user();

        $tareasGenerales = TareaGeneral::whereIn(
            'id',
            SubTarea::where('user_id', $user->id)->pluck('tarea_general_id')
        )
            ->with([
                'subtareas' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            ])
            ->get();

        return response()->json($tareasGenerales);
    }
}