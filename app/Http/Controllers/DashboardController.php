<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\User;
use App\Models\Task;
use App\Models\SubTarea;
use App\Models\Visita;
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

        // Apiarios para data gráfica (solo trashumantes activos)
        $apiarios = Apiario::where('user_id', $user->id)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->get();

        $dataApiarios = $apiarios->map(fn($a) => [
            'name' => $a->nombre,
            'count' => $a->num_colmenas,
            'season' => $a->temporada_produccion,
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
                'apiario' => $v->apiario->nombre,
                'tipo_visita' => $v->tipo_visita,
            ]);

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
