<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\User;
use App\Models\Task;
use App\Models\SubTarea;
use App\Models\Visita;
use Auth;

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

        // 1. Defino los tipos de visita que quiero contar
        $tiposOk = [
            'Visita General',
            'Inspección de Visita',
            'Uso de Medicamentos',
        ];

        // 2. Conteo total de apiarios y colmenas (igual que antes)
        $totalApiarios = Apiario::where('user_id', $user->id)->count();
        $totalColmenas = Apiario::where('user_id', $user->id)->sum('num_colmenas');

        // 3. Ahora cuento SOLO las visitas de esos tipos
        $visitas = Visita::whereHas('apiario', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->whereIn('tipo_visita', $tiposOk)
            ->count();

        // 4. El resto de tu lógica (tasks, dataApiarios, etc.) no cambia
        $tasks = SubTarea::where('user_id', $user->id)->get();
        $t_progreso = $tasks->where('estado', 'En progreso')->count();
        $t_pendientes = $tasks->where('estado', 'Pendiente')->count();
        $t_urgentes = $tasks->where('prioridad', 'urgente')
            ->where('estado', '!=', 'Completada')
            ->count();
        $t_completadas = $tasks->where('estado', 'Completada')->count();

        $apiarios = Apiario::where('user_id', $user->id)->get();
        $dataApiarios = $apiarios->map(fn($a) => [
            'name' => $a->nombre,
            'count' => $a->num_colmenas,
            'season' => $a->temporada_produccion,
        ]);

        // Si necesitas usar el detalle de visitas en gráficos, corrige también el mapeo:
        $dataVisitas = Visita::whereHas(
            'apiario',
            fn($q) =>
            $q->where('user_id', $user->id)
        )
            ->whereIn('tipo_visita', $tiposOk)    // opcional: solo pasar esos tres tipos
            ->get()
            ->map(fn($v) => [
                'apiario' => $v->apiario->nombre,
                'tipo_visita' => $v->tipo_visita,   // aquí estaba '$v->tipo'
            ]);

        return view('dashboard', compact(
            'totalApiarios',
            'totalColmenas',
            'visitas',           // ahora ya es el conteo filtrado
            't_progreso',
            't_pendientes',
            't_urgentes',
            't_completadas',
            'dataApiarios',
            'dataVisitas',
            'user'
        ));
    }


    public function home()
    {
        $user = Auth::user();
        if (!$user) {
            // Redirigimos a la página de login o mostramos un mensaje de error, según prefieras
            return redirect()->route('welcome')->with('error', 'Debe iniciar sesión para acceder al dashboard.');
        }
        // Obtenemos la cantidad de apiarios y reemplazamos con 0 si es null
        $totalApiarios = Apiario::where('user_id', $user->id)->count() ?? 0;

        // Obtenemos la cantidad de colmenas asociadas al usuario, reemplazando con 0 si es null
        $totalColmenas = Apiario::where('user_id', $user->id)->sum('num_colmenas');

        // Definimos valores predeterminados para variables que podrían ser nulas
        $ubicacionApiarios = 'Zona Norte'; // Modifica esto según tu lógica de ubicación
        $clima = 'Soleado'; // Puedes integrar una API para obtener el clima real si es necesario
        $apiarioEnUso = 'Apiario 1'; // Debe ser dinámico según tus necesidades
        $visitas = 0; // Valor predeterminado para visitas si no existen
        $tasks = SubTarea::where('user_id', $user->id)->get();
        // Contar tareas con manejo de casos donde no haya tareas
        $t_progreso = $tasks->where('estado', 'En progreso')->count() ?? 0;
        $t_pendientes = $tasks->where('estado', 'Pendiente')->count() ?? 0;
        $t_urgentes = $tasks->where('prioridad', 'urgente')->where('estado', '!=', 'Completada')->count() ?? 0;
        $t_completadas = $tasks->where('estado', 'Completada')->count() ?? 0;
        // Retornamos la vista 'dashboard' con las variables aseguradas
        $totalVisitas = Visita::whereHas('apiario', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        $visitas = $totalVisitas ?? 0;
        $apiarios = Apiario::where('user_id', $user->id)->get();
        $dataApiarios = $apiarios->map(function ($apiario) {
            return [
                'name' => $apiario->nombre,
                'count' => $apiario->num_colmenas,
                'season' => $apiario->temporada_produccion
            ];
        });
        return view('home', compact('totalApiarios', 'totalColmenas', 'ubicacionApiarios', 'clima', 'apiarioEnUso', 'visitas', 't_progreso', 't_pendientes', 't_urgentes', 'dataApiarios', 't_completadas', 'user'));
    }

    //cantidad de apiarios:
    public function cantidadApiarios()
    {
        // Obtener la cantidad de apiarios para el usuario autenticado
        // Pasar la cantidad a la vista
        return view('dashboard', compact('cantidadApiarios'));
    }
}
