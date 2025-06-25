<?php

namespace App\Http\Controllers;

use App\Models\Apiario;
use Illuminate\Http\Request;
use Auth;

class ZonificacionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Apiarios fijos del usuario
        $apiariosFijos = $user->apiarios()
            ->where('tipo_apiario', 'fijo')
            ->get(['id', 'latitud', 'longitud', 'tipo_apiario', 'num_colmenas', 'nombre', 'foto']);

        // Apiarios base (trashumantes, activos, no temporales)
        $apiariosBase = $user->apiarios()
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->where('es_temporal', false)
            ->get(['id', 'latitud', 'longitud', 'tipo_apiario', 'num_colmenas', 'nombre', 'foto']);

        // Apiarios temporales (trashumantes, activos, temporales)
        $apiariosTemporales = $user->apiarios()
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->where('es_temporal', true)
            ->get(['id', 'latitud', 'longitud', 'tipo_apiario', 'num_colmenas', 'nombre', 'foto']);

        // Apiarios archivados (del usuario, activos = 0)
        $apiariosArchivados = $user->apiarios()
            ->where('activo', 0)
            ->get(['id', 'latitud', 'longitud', 'tipo_apiario', 'num_colmenas', 'nombre', 'foto']);

        // Calcular totales de colmenas por sección
        $totalColmenasFijos = $apiariosFijos->sum('num_colmenas');
        $totalColmenasBase = $apiariosBase->sum('num_colmenas');
        $totalColmenasTemporales = $apiariosTemporales->sum('num_colmenas');
        $totalColmenasArchivadas = $apiariosArchivados->sum('num_colmenas');

        // Total general de colmenas activas
        $totalColmenasActivas = $totalColmenasFijos + $totalColmenasBase + $totalColmenasTemporales;

        return view('zonificacion.index', compact(
            'apiariosFijos',
            'apiariosBase',
            'apiariosTemporales',
            'apiariosArchivados',
            'totalColmenasFijos',
            'totalColmenasBase',
            'totalColmenasTemporales',
            'totalColmenasArchivadas',
            'totalColmenasActivas'
        ));
    }
}