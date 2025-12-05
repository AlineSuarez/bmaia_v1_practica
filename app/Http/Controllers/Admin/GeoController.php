<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apiario;
use Illuminate\Support\Facades\DB;

class GeoController extends Controller
{
    public function index()
    {
        // Obtener todos los apiarios con sus coordenadas
        $apiarios = Apiario::with('user')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->where('activo', 1)
            ->get();

        // Estadísticas por región
        $apiariosPorRegion = Apiario::join('comunas', 'apiarios.comuna_id', '=', 'comunas.id')
            ->join('regiones', 'comunas.region_id', '=', 'regiones.id')
            ->select('regiones.nombre as region', DB::raw('COUNT(*) as total'))
            ->where('apiarios.activo', 1)
            ->groupBy('regiones.id', 'regiones.nombre')
            ->orderByDesc('total')
            ->get();

        // Total de usuarios registrados en el sistema
        $totalUsuarios = \App\Models\User::count();

        return view('admin.georeferenciacion', compact('apiarios', 'apiariosPorRegion', 'totalUsuarios'));
    }
}
