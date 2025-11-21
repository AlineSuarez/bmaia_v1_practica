<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\MovimientoColmena;
use App\Models\User;
use App\Models\Task;
use App\Models\TareaGeneral;
use App\Models\SubTarea;
use App\Models\Visita;
use App\Models\VisitaInspeccion;
use App\Models\PresenciaVarroa;
use App\Models\EstadoNutricional;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()
                ->route('welcome')
                ->with('error', 'Debe iniciar sesión para acceder al dashboard.');
        }

        $tiposOk = [
            'Visita General',
            'Inspección de Visita',
            'Uso de Medicamentos',
            'Alimentación',
            'Inspección de Reina',
        ];

        // Apiarios base (trashumantes activos y no temporales)
        $apiariosBase = Apiario::where('user_id', $user->id)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->where('es_temporal', false)
            ->count();

        // Apiarios temporales (trashumantes activos y temporales)
        $apiariosTemporales = Apiario::where('user_id', $user->id)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->where('es_temporal', true)
            ->count();

        // Totales (solo trashumantes activos)
        $totalApiarios = Apiario::where('user_id', $user->id)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->count();

        $totalColmenas = Apiario::where('user_id', $user->id)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->sum('num_colmenas');

        // Visitas (solo en trashumantes activos)
        $visitas = Visita::whereHas('apiario', function ($q) use ($user) {
            $q->where('user_id', $user->id)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1);
        })
            ->whereIn('tipo_visita', $tiposOk)
            ->count();

        // Tareas
        $tasks = SubTarea::where('user_id', $user->id)->get();
        $t_progreso = $tasks->where('estado', 'En progreso')->count();
        $t_pendientes = $tasks->where('estado', 'Pendiente')->count();
        $t_urgentes = $tasks->where('prioridad', 'urgente')
            ->where('estado', '!=', 'Completada')
            ->count();
        $t_completadas = $tasks->where('estado', 'Completada')->count();

        $tareasGenerales = TareaGeneral::select('id', 'nombre')->get();

        // Apiarios para data gráfica (solo trashumantes activos)
        $apiarios = Apiario::where('user_id', $user->id)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->get();

        $dataApiarios = $apiarios->map(fn($a) => [
            'id' => $a->id,
            'name' => $a->nombre,
            'count' => $a->num_colmenas,
            'season' => $a->temporada_produccion,
            'latitud' => $a->latitud,
            'longitud' => $a->longitud,
            'actividad' => $a->objetivo_produccion,
        ]);

        // Data visitas
        $dataVisitas = Visita::whereHas('apiario', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('tipo_apiario', 'trashumante')
              ->where('activo', 1);
        })
        ->whereIn('tipo_visita', $tiposOk)
        ->get()
        ->map(fn($v) => [
            'id' => $v->id,
            'apiario_id' => $v->apiario_id,
            'apiario' => $v->apiario->nombre,
        
            'fecha_visita' => $v->fecha_visita,
            'tipo_visita' => $v->tipo_visita,
            'motivo_tratamiento' => $v->motivo_tratamiento ?? null,
        ]);

        // Extraer inspecciones asociadas a las visitas obtenidas
        $dataVisitasInspecciones = VisitaInspeccion::whereIn(
            'visita_id',
            $dataVisitas->pluck('id')
        )->get()
        ->map(fn($i) => [
            'visita_id' => $i->visita_id,
            'num_colmenas_totales' => $i->num_colmenas_totales,
            'num_colmenas_inspeccionadas' => $i->num_colmenas_inspeccionadas,
            'num_colmenas_enfermas' => $i->num_colmenas_enfermas,
            'num_colmenas_activas' => $i->num_colmenas_activas,
            'num_colmenas_muertas' => $i->num_colmenas_muertas,
            'created_at' => $i->created_at->format('Y-m-d'),
        ]);

        // Datos de presencia de Varroa por visita    
        $presenciaVarroa = PresenciaVarroa::whereIn(
            'visita_id',
            $dataVisitas->pluck('id')
        )->get();      
            
        // Datos de alimentacion por visita
        $estadoNutricional = EstadoNutricional::whereIn(
            'visita_id',
            $dataVisitas->pluck('id')
        )->get();        

        // Estado de tareas
        $estadoTareas = [
            'Completada' => $tasks->where('estado','Completada')->count(),
            'En progreso' => $tasks->where('estado','En progreso')->count(),
            'Pendiente' => $tasks->where('estado','Pendiente')->count(),
            'Vencida' => $tasks
                ->filter(fn($t) => $t->fecha_limite && $t->fecha_limite < now() && $t->estado != 'Completada')
                ->count()
        ];

        $subtareasEstadoTareas = collect($estadoTareas)->map(fn($count,$estado) => [
            'name'=>$estado,
            'value'=>$count
        ])->values();
        
        // Agrupar subtareas por fase
        $planFases = $tasks->groupBy(fn($t) => $t->fase ?? 'General');
        
        // Cumplimiento plan anual
        $cumplimientoPlan = $planFases->map(fn($tareas,$fase)=>[
            'fase'=>$fase,
            'total'=>$tareas->count(),
            'completadas'=>$tareas->where('estado','Completada')->count(),
            'porcentaje'=>$tareas->count()
                ? round($tareas->where('estado','Completada')->count() / $tareas->count() * 100)
                : 0
        ])->values();

        // Obtener movimientos vinculados a colmenas de los apiarios del usuario
        $apiariosUser = Apiario::where('user_id', $user->id)
        ->where('activo', 1)
        ->where('tipo_apiario', 'trashumante')
        ->pluck('id');
        
        $colmenasUser = Colmena::whereIn('apiario_id', $apiariosUser)->pluck('id');

        $movimientosColmenas = MovimientoColmena::whereIn('colmena_id', $colmenasUser)->get();

        return view('dashboard', compact(
            'totalApiarios',
            'totalColmenas',
            'visitas',
            't_progreso',
            't_pendientes',
            't_urgentes',
            't_completadas',
            'dataApiarios',
            'dataVisitas',
            'dataVisitasInspecciones',
            'presenciaVarroa',
            'estadoNutricional',
            'subtareasEstadoTareas',
            'tasks',
            'tareasGenerales',
            'movimientosColmenas',
            'user',
            'apiariosBase',
            'apiariosTemporales'
        ));
    }

    public function home()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('welcome')->with('error', 'Debe iniciar sesión para acceder al dashboard.');
        }

        // Total de apiarios (solo trashumantes activos)
        $totalApiarios = Apiario::where('user_id', $user->id)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->count();

        // Total de colmenas (solo trashumantes activos)
        $totalColmenas = Apiario::where('user_id', $user->id)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->sum('num_colmenas');

        // Variables predeterminadas
        $ubicacionApiarios = 'Zona Norte';
        $clima = 'Soleado';
        $apiarioEnUso = 'Apiario 1';

        // Tareas
        $tasks = SubTarea::where('user_id', $user->id)->get();
        $t_progreso = $tasks->where('estado', 'En progreso')->count();
        $t_pendientes = $tasks->where('estado', 'Pendiente')->count();
        $t_urgentes = $tasks->where('prioridad', 'urgente')->where('estado', '!=', 'Completada')->count();
        $t_completadas = $tasks->where('estado', 'Completada')->count();

        // Visitas (solo trashumantes activos)
        $totalVisitas = Visita::whereHas('apiario', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('tipo_apiario', 'trashumante')
                ->where('activo', 1);
        })->count();
        $visitas = $totalVisitas ?? 0;

        // Apiarios para data gráfica (solo trashumantes activos)
        $apiarios = Apiario::where('user_id', $user->id)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->get();
            
        $dataApiarios = $apiarios->map(function ($apiario) {
            return [
                'name' => $apiario->nombre,
                'count' => $apiario->num_colmenas,
                'season' => $apiario->temporada_produccion
            ];
        });

        // Plan del usuario
        $ultimaFactura = $user->facturas()
            ->where('estado', 'emitida')
            ->latest('fecha_emision')
            ->first();

        $plan_end_date = $ultimaFactura
            ? Carbon::parse($ultimaFactura->fecha_emision)->addDays(365)
            : null;

        $payment = Payment::where('user_id', $user->id)
            ->where('status', 'paid')
            ->latest()
            ->first();

        $planLabel = 'Sin plan activo';
        $plan_end_date = null;
        $plan_active = false;

        if ($payment) {
            if ($payment->plan === 'drone') {
                $trialEnd = $payment->created_at->copy()->addDays(16);
                if (now()->lessThan($trialEnd)) {
                    $planLabel = 'Drone';
                    $plan_end_date = $trialEnd;
                    $plan_active = true;
                }
            } else {
                $expiresAt = $payment->expires_at ?? $payment->created_at->copy()->addYear();
                if (now()->lessThan($expiresAt)) {
                    $planLabel = strtoupper($payment->plan);
                    $plan_end_date = $expiresAt;
                    $plan_active = true;
                }
            }
        }

        return view('home', compact(
            'totalApiarios',
            'totalColmenas',
            'ubicacionApiarios',
            'clima',
            'apiarioEnUso',
            'visitas',
            't_progreso',
            't_pendientes',
            't_urgentes',
            'dataApiarios',
            't_completadas',
            'user',
            'planLabel',
            'plan_end_date',
            'plan_active'
        ));
    }


    //cantidad de apiarios:
    public function cantidadApiarios()
    {
        // Obtener la cantidad de apiarios para el usuario autenticado
        // Pasar la cantidad a la vista
        return view('dashboard', compact('cantidadApiarios'));
    }
}
