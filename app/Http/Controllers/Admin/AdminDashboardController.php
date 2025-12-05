<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\Visita;
use App\Models\MovimientoColmena;
use App\Models\Tratamiento;
use App\Models\Produccion;
use App\Models\Alimentacion;
use App\Models\IndiceCosecha;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminDashboardController extends Controller
{
    /**
     * Orden correlativo de las regiones de Chile
     */
    private function getRegionesOrdenadas()
    {
        return [
            'Región de Arica y Parinacota',
            'Región de Tarapacá',
            'Región de Antofagasta',
            'Región de Atacama',
            'Región de Coquimbo',
            'Región de Valparaíso',
            'Región Metropolitana de Santiago',
            'Región del Libertador General Bernardo O\'Higgins',
            'Región del Maule',
            'Región de Ñuble',
            'Región del Biobío',
            'Región de La Araucanía',
            'Región de Los Ríos',
            'Región de Los Lagos',
            'Región de Aysén del General Carlos Ibáñez del Campo',
            'Región de Magallanes y de la Antártica Chilena'
        ];
    }

    /**
     * Simplifica los nombres de las regiones para mostrar en gráficos
     */
    private function simplificarNombreRegion($nombreCompleto)
    {
        $mapeo = [
            'Región de Arica y Parinacota' => 'Arica',
            'Región de Tarapacá' => 'Tarapacá',
            'Región de Antofagasta' => 'Antofagasta',
            'Región de Atacama' => 'Atacama',
            'Región de Coquimbo' => 'Coquimbo',
            'Región de Valparaíso' => 'Valparaíso',
            'Región Metropolitana de Santiago' => 'RM',
            'Región del Libertador General Bernardo O\'Higgins' => 'O\'Higgins',
            'Región del Maule' => 'Maule',
            'Región de Ñuble' => 'Ñuble',
            'Región del Biobío' => 'Biobío',
            'Región de La Araucanía' => 'Araucanía',
            'Región de Los Ríos' => 'Los Ríos',
            'Región de Los Lagos' => 'Los Lagos',
            'Región de Aysén del General Carlos Ibáñez del Campo' => 'Aysén',
            'Región de Magallanes y de la Antártica Chilena' => 'Magallanes'
        ];

        return $mapeo[$nombreCompleto] ?? $nombreCompleto;
    }

    /**
     * Completa datos faltantes con ceros para todas las regiones
     */
    private function completarRegiones($datos, $campoValor = 'total')
    {
        $regionesOrdenadas = $this->getRegionesOrdenadas();
        $resultado = [];

        foreach ($regionesOrdenadas as $regionNombre) {
            $encontrado = false;
            foreach ($datos as $dato) {
                if ($dato->region === $regionNombre) {
                    // Simplificar el nombre de la región
                    $datoClonado = clone $dato;
                    $datoClonado->region = $this->simplificarNombreRegion($regionNombre);
                    $resultado[] = $datoClonado;
                    $encontrado = true;
                    break;
                }
            }

            if (!$encontrado) {
                $obj = new \stdClass();
                $obj->region = $this->simplificarNombreRegion($regionNombre);
                $obj->$campoValor = 0;
                $resultado[] = $obj;
            }
        }

        return collect($resultado);
    }

    public function index()
    {
        // ========================================
        // 1. PRODUCTIVIDAD GLOBAL
        // ========================================

        // 1.1 Número Promedio de Colmenas por Apiario
        $promedioColmenasPorApiario = Apiario::where('activo', 1)
            ->avg('num_colmenas') ?? 0;

        // 1.2 Tasa de Supervivencia (% de colmenas activas)
        // Nota: En este sistema, las colmenas activas son las que no están eliminadas (soft delete)
        $totalColmenasConEliminadas = Colmena::withTrashed()->count();
        $colmenasActivas = Colmena::count(); // Sin soft delete
        $tasaSupervivencia = $totalColmenasConEliminadas > 0
            ? ($colmenasActivas / $totalColmenasConEliminadas) * 100
            : 100; // Si no hay colmenas eliminadas, supervivencia es 100%

        // 1.3 Promedio de Tratamientos por Colmena/Año
        // Suma el total de colmenas tratadas en todos los registros
        $totalColmenasTratadas = Tratamiento::sum('num_colmenas_tratadas');
        $promedioTratamientos = $colmenasActivas > 0
            ? $totalColmenasTratadas / $colmenasActivas
            : 0;

        // 1.4 Producción Anual Promedio (kg/colmena/año)
        // Estimación automática desde índice de cosecha (no requiere registro manual)

        // Producción estimada desde índice de cosecha (1.5 kg por marco de miel)
        // Cada marco con miel operculada produce aproximadamente 1.5 kg de miel
        $totalProduccion = IndiceCosecha::sum('marcos_miel') * 1.5;

        $promedioProduccion = $colmenasActivas > 0
            ? $totalProduccion / $colmenasActivas
            : 0;

        // 1.5 Tasa de Mortalidad (%)
        $tasaMortalidad = $totalColmenasConEliminadas > 0
            ? (($totalColmenasConEliminadas - $colmenasActivas) / $totalColmenasConEliminadas) * 100
            : 0;

        // 1.6 Alimento Suministrado (kg/colmena/año)
        $totalAlimento = Alimentacion::sum('cantidad_kg');
        $totalColmenasAlimentadas = Alimentacion::sum('num_colmenas');
        $alimentoSuministrado = $totalColmenasAlimentadas > 0
            ? $totalAlimento / $totalColmenasAlimentadas
            : 0;

        // 1.7 Promedio de Visitas por Apiario
        $totalVisitas = Visita::count();
        $totalApiariosConVisitas = Apiario::where('activo', 1)->count();
        $promedioVisitasPorApiario = $totalApiariosConVisitas > 0
            ? $totalVisitas / $totalApiariosConVisitas
            : 0;

        // 1.8 Tipo de Actividad de los Apiarios (para gráfico)
        $actividadesApiarios = Apiario::where('activo', 1)
            ->select('objetivo_produccion', DB::raw('COUNT(*) as total'))
            ->groupBy('objetivo_produccion')
            ->get();

        // ========================================
        // 2. PRODUCTIVIDAD POR APIARIO
        // ========================================

        // 2.1 Total de Apiarios Activos
        $totalApiariosActivos = Apiario::where('activo', 1)->count();

        // 2.2 Regiones con Apiarios
        $regionesConApiarios = Apiario::where('activo', 1)
            ->distinct('region_id')
            ->count('region_id');

        // 2.3 Porcentaje de Apiarios Geolocalizados
        $apiariosConGeo = Apiario::where('activo', 1)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->count();
        $porcentajeApiariosGeolocalizados = $totalApiariosActivos > 0
            ? ($apiariosConGeo / $totalApiariosActivos) * 100
            : 0;

        // 2.4 Distribución de Colmenas por Región (para gráfico)
        $colmenasPorRegionRaw = DB::table('apiarios')
            ->join('regiones', 'apiarios.region_id', '=', 'regiones.id')
            ->where('apiarios.activo', 1)
            ->select('regiones.nombre as region', DB::raw('SUM(apiarios.num_colmenas) as total_colmenas'))
            ->groupBy('regiones.id', 'regiones.nombre')
            ->get();

        // Completar con todas las regiones en orden correlativo
        $colmenasPorRegion = $this->completarRegiones($colmenasPorRegionRaw, 'total_colmenas');

        // 2.5 Tipo de Alimento Utilizado (para gráfico)
        // Suma los kg totales por tipo de alimento (no cuenta registros)
        $tiposAlimento = Alimentacion::select('tipo_alimentacion', DB::raw('SUM(cantidad_kg) as total_kg'))
            ->groupBy('tipo_alimentacion')
            ->get()
            ->map(function($item) {
                // Convierte los tipos a etiquetas legibles
                $labels = [
                    'azucar' => 'Azúcar/Jarabe',
                    'jarabe' => 'Jarabe',
                    'fondant' => 'Fondant',
                    'pasta' => 'Pasta Proteica',
                    'polen' => 'Polen',
                    'sustituto_polen' => 'Sustituto de Polen',
                    'otro' => 'Otro'
                ];
                $item->tipo_alimentacion = $labels[$item->tipo_alimentacion] ?? ucfirst($item->tipo_alimentacion);
                return $item;
            });

        // 2.6 Distribución de Apicultores por Región (para gráfico)
        // Agrupa por la región de residencia del apicultor (users.id_region), no por ubicación de apiarios
        $apicultoresPorRegionRaw = DB::table('users')
            ->join('regiones', 'users.id_region', '=', 'regiones.id')
            ->whereNotNull('users.id_region')
            ->where('users.role', '!=', 'admin') // Excluir administradores
            ->select('regiones.nombre as region', DB::raw('COUNT(users.id) as total_apicultores'))
            ->groupBy('regiones.id', 'regiones.nombre')
            ->get();

        // Completar con todas las regiones en orden correlativo
        $apicultoresPorRegion = $this->completarRegiones($apicultoresPorRegionRaw, 'total_apicultores');

        // ========================================
        // 3. MOVIMIENTO DE COLMENAS
        // ========================================

        // 3.1 Total de Movimientos
        $totalMovimientos = MovimientoColmena::count();

        // 3.2 Total de Trashumancia (movimientos a apiarios temporales)
        $totalTrashumancia = MovimientoColmena::where('tipo_movimiento', 'trashumancia')->count();

        // 3.3 Total de Retornos (movimientos de vuelta a base)
        $totalRetornos = MovimientoColmena::where('tipo_movimiento', 'retorno')->count();

        // 3.4 Duración Total del Servicio de Polinización (días)
        // Solo cuenta apiarios temporales activos con motivo de polinización
        $movimientosPolinizacion = MovimientoColmena::where('motivo_movimiento', 'Polinización')
            ->whereNotNull('fecha_inicio_mov')
            ->whereNotNull('fecha_termino_mov')
            ->whereHas('apiarioDestino', function($query) {
                $query->where('es_temporal', 1)
                      ->where('activo', 1);
            })
            ->get();

        $duracionPromedioPolinizacion = $movimientosPolinizacion->map(function($movimiento) {
            $inicio = \Carbon\Carbon::parse($movimiento->fecha_inicio_mov);
            $fin = \Carbon\Carbon::parse($movimiento->fecha_termino_mov);

            // Asegurarse de que ambas fechas estén en la misma zona horaria
            $inicio->startOfDay();
            $fin->startOfDay();

            return $inicio->diffInDays($fin);
        })->sum();

        $duracionPromedioPolinizacion = $duracionPromedioPolinizacion ? $duracionPromedioPolinizacion : 0;

        // 3.5 Movimientos por Motivo (para gráfico de torta: Producción vs Polinización)
        // Excluir "Retorno por archivado múltiple"
        $movimientosPorMotivo = MovimientoColmena::select('motivo_movimiento', DB::raw('COUNT(*) as total'))
            ->whereNotNull('motivo_movimiento')
            ->where('motivo_movimiento', '!=', 'Retorno por archivado múltiple')
            ->groupBy('motivo_movimiento')
            ->get();

        // ========================================
        // DATOS ADICIONALES PARA GRÁFICOS ANTIGUOS (compatibilidad)
        // ========================================

        // Preparar datos para gráficos de colmenas por apiario
        $dataApiarios = Apiario::where('activo', 1)
            ->select('nombre', 'num_colmenas')
            ->get()
            ->map(function($apiario) {
                return [
                    'nombre' => $apiario->nombre,
                    'colmenas' => $apiario->num_colmenas
                ];
            });

        // Preparar datos para gráfico de visitas
        $dataVisitas = DB::table('visitas')
            ->join('apiarios', 'visitas.apiario_id', '=', 'apiarios.id')
            ->where('apiarios.activo', 1)
            ->select('apiarios.nombre', DB::raw('COUNT(visitas.id) as total'))
            ->groupBy('apiarios.id', 'apiarios.nombre')
            ->get()
            ->map(function($item) {
                return [
                    'apiario' => $item->nombre,
                    'visitas' => $item->total
                ];
            });

        return view('admin.dashboard', compact(
            // 1. PRODUCTIVIDAD GLOBAL
            'promedioColmenasPorApiario',
            'tasaSupervivencia',
            'promedioTratamientos',
            'promedioProduccion',
            'tasaMortalidad',
            'alimentoSuministrado',
            'promedioVisitasPorApiario',
            'actividadesApiarios',

            // 2. PRODUCTIVIDAD POR APIARIO
            'totalApiariosActivos',
            'regionesConApiarios',
            'porcentajeApiariosGeolocalizados',
            'colmenasPorRegion',
            'tiposAlimento',
            'apicultoresPorRegion',

            // 3. MOVIMIENTO DE COLMENAS
            'totalMovimientos',
            'totalTrashumancia',
            'totalRetornos',
            'duracionPromedioPolinizacion',
            'movimientosPorMotivo',

            // Datos para gráficos (compatibilidad)
            'dataApiarios',
            'dataVisitas'
        ));
    }

    public function generatePdf()
    {
        // ========================================
        // 1. PRODUCTIVIDAD GLOBAL
        // ========================================

        // 1.1 Número Promedio de Colmenas por Apiario
        $promedioColmenasPorApiario = Apiario::where('activo', 1)
            ->avg('num_colmenas') ?? 0;

        // 1.2 Tasa de Supervivencia (% de colmenas activas)
        $totalColmenasConEliminadas = Colmena::withTrashed()->count();
        $colmenasActivas = Colmena::count();
        $tasaSupervivencia = $totalColmenasConEliminadas > 0
            ? ($colmenasActivas / $totalColmenasConEliminadas) * 100
            : 100;

        // 1.3 Promedio de Tratamientos por Colmena/Año
        $totalColmenasTratadas = Tratamiento::sum('num_colmenas_tratadas');
        $promedioTratamientos = $colmenasActivas > 0
            ? $totalColmenasTratadas / $colmenasActivas
            : 0;

        // 1.4 Producción Anual Promedio (kg/colmena/año)
        $totalProduccion = IndiceCosecha::sum('marcos_miel') * 1.5;
        $promedioProduccion = $colmenasActivas > 0
            ? $totalProduccion / $colmenasActivas
            : 0;

        // 1.5 Tasa de Mortalidad (%)
        $tasaMortalidad = $totalColmenasConEliminadas > 0
            ? (($totalColmenasConEliminadas - $colmenasActivas) / $totalColmenasConEliminadas) * 100
            : 0;

        // 1.6 Alimento Suministrado (kg/colmena/año)
        $totalAlimento = Alimentacion::sum('cantidad_kg');
        $totalColmenasAlimentadas = Alimentacion::sum('num_colmenas');
        $alimentoSuministrado = $totalColmenasAlimentadas > 0
            ? $totalAlimento / $totalColmenasAlimentadas
            : 0;

        // 1.7 Promedio de Visitas por Apiario
        $totalVisitas = Visita::count();
        $totalApiariosConVisitas = Apiario::where('activo', 1)->count();
        $promedioVisitasPorApiario = $totalApiariosConVisitas > 0
            ? $totalVisitas / $totalApiariosConVisitas
            : 0;

        // 1.8 Tipo de Actividad de los Apiarios
        $actividadesApiarios = Apiario::where('activo', 1)
            ->select('objetivo_produccion', DB::raw('COUNT(*) as total'))
            ->groupBy('objetivo_produccion')
            ->get();

        // ========================================
        // 2. PRODUCTIVIDAD POR APIARIO
        // ========================================

        // 2.1 Total de Apiarios Activos
        $totalApiariosActivos = Apiario::where('activo', 1)->count();

        // 2.2 Regiones con Apiarios
        $regionesConApiarios = Apiario::where('activo', 1)
            ->distinct('region_id')
            ->count('region_id');

        // 2.3 Porcentaje de Apiarios Geolocalizados
        $apiariosConGeo = Apiario::where('activo', 1)
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->count();
        $porcentajeApiariosGeolocalizados = $totalApiariosActivos > 0
            ? ($apiariosConGeo / $totalApiariosActivos) * 100
            : 0;

        // 2.4 Distribución de Colmenas por Región
        $colmenasPorRegion = DB::table('apiarios')
            ->join('regiones', 'apiarios.region_id', '=', 'regiones.id')
            ->where('apiarios.activo', 1)
            ->select('regiones.nombre as region', DB::raw('SUM(apiarios.num_colmenas) as total_colmenas'))
            ->groupBy('regiones.id', 'regiones.nombre')
            ->orderBy('total_colmenas', 'desc')
            ->get();

        // 2.5 Tipo de Alimento Utilizado
        $tiposAlimento = Alimentacion::select('tipo_alimentacion', DB::raw('SUM(cantidad_kg) as total_kg'))
            ->groupBy('tipo_alimentacion')
            ->get()
            ->map(function($item) {
                $labels = [
                    'azucar' => 'Azúcar/Jarabe',
                    'jarabe' => 'Jarabe',
                    'fondant' => 'Fondant',
                    'pasta' => 'Pasta Proteica',
                    'polen' => 'Polen',
                    'sustituto_polen' => 'Sustituto de Polen',
                    'otro' => 'Otro'
                ];
                $item->tipo_alimentacion = $labels[$item->tipo_alimentacion] ?? ucfirst($item->tipo_alimentacion);
                return $item;
            });

        // 2.6 Distribución de Apicultores por Región
        $apicultoresPorRegion = DB::table('users')
            ->join('regiones', 'users.id_region', '=', 'regiones.id')
            ->whereNotNull('users.id_region')
            ->where('users.role', '!=', 'admin')
            ->select('regiones.nombre as region', DB::raw('COUNT(users.id) as total_apicultores'))
            ->groupBy('regiones.id', 'regiones.nombre')
            ->orderBy('total_apicultores', 'desc')
            ->get();

        // ========================================
        // 3. MOVIMIENTO DE COLMENAS
        // ========================================

        // 3.1 Total de Movimientos
        $totalMovimientos = MovimientoColmena::count();

        // 3.2 Total de Trashumancia
        $totalTrashumancia = MovimientoColmena::where('tipo_movimiento', 'trashumancia')->count();

        // 3.3 Total de Retornos
        $totalRetornos = MovimientoColmena::where('tipo_movimiento', 'retorno')->count();

        // 3.4 Duración Total del Servicio de Polinización (días)
        // Solo cuenta apiarios temporales activos con motivo de polinización
        $movimientosPolinizacion = MovimientoColmena::where('motivo_movimiento', 'Polinización')
            ->whereNotNull('fecha_inicio_mov')
            ->whereNotNull('fecha_termino_mov')
            ->whereHas('apiarioDestino', function($query) {
                $query->where('es_temporal', 1)
                      ->where('activo', 1);
            })
            ->get();

        $duracionPromedioPolinizacion = $movimientosPolinizacion->map(function($movimiento) {
            $inicio = \Carbon\Carbon::parse($movimiento->fecha_inicio_mov);
            $fin = \Carbon\Carbon::parse($movimiento->fecha_termino_mov);

            // Asegurarse de que ambas fechas estén en la misma zona horaria
            $inicio->startOfDay();
            $fin->startOfDay();

            return $inicio->diffInDays($fin);
        })->sum();

        $duracionPromedioPolinizacion = $duracionPromedioPolinizacion ? $duracionPromedioPolinizacion : 0;

        // 3.5 Movimientos por Motivo (para gráfico de torta: Producción vs Polinización)
        // Excluir "Retorno por archivado múltiple"
        $movimientosPorMotivo = MovimientoColmena::select('motivo_movimiento', DB::raw('COUNT(*) as total'))
            ->whereNotNull('motivo_movimiento')
            ->where('motivo_movimiento', '!=', 'Retorno por archivado múltiple')
            ->groupBy('motivo_movimiento')
            ->get();

        $data = compact(
            'promedioColmenasPorApiario',
            'tasaSupervivencia',
            'promedioTratamientos',
            'promedioProduccion',
            'tasaMortalidad',
            'alimentoSuministrado',
            'promedioVisitasPorApiario',
            'actividadesApiarios',
            'totalApiariosActivos',
            'regionesConApiarios',
            'porcentajeApiariosGeolocalizados',
            'colmenasPorRegion',
            'tiposAlimento',
            'apicultoresPorRegion',
            'totalMovimientos',
            'totalTrashumancia',
            'totalRetornos',
            'duracionPromedioPolinizacion',
            'movimientosPorMotivo'
        );

        $pdf = Pdf::loadView('admin.dashboard-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('reporte-dashboard-bmaia-' . date('Y-m-d') . '.pdf');
    }
}
