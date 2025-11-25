<?php

namespace App\Http\Controllers;

use App\Models\Apiario;
use App\Models\Flor;
use App\Models\Fenofase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        // Totales
        $totalColmenasFijos       = $apiariosFijos->sum('num_colmenas');
        $totalColmenasBase        = $apiariosBase->sum('num_colmenas');
        $totalColmenasTemporales  = $apiariosTemporales->sum('num_colmenas');
        $totalColmenasArchivadas  = $apiariosArchivados->sum('num_colmenas');
        $totalColmenasActivas     = $totalColmenasFijos + $totalColmenasBase + $totalColmenasTemporales;

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

    // Vista individual del apiario + catálogo de floraciones
    public function show($id)
    {
        $user = Auth::user();

        // Seguridad: que el apiario sea del usuario
        $apiario = Apiario::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Catálogo de flores
        $especies = Flor::orderBy('nombre')
            ->get(['id', 'nombre', 'slug']);

        // Fenofases (id, clave, nombre, orden)
        $fenofases = Fenofase::orderBy('orden')
            ->get(['id', 'clave', 'nombre', 'orden']);

        // Diccionarios de ayuda
        $faseKeyById  = $fenofases->pluck('clave', 'id');  // ej: [1=>'boton', 2=>'inicio', ...]
        $slugByFlorId = $especies->pluck('slug', 'id');    // ej: [5=>'tevo', 6=>'quillay', ...]

        // Imágenes (flor_id, fenofase_id, path) => [flor_id][fenofase_clave] = path relativo
        $imagenes = DB::table('flor_imagenes')->get(['flor_id', 'fenofase_id', 'path']);

        $imgByFlorAndFase = [];
        foreach ($imagenes as $img) {
            $clave = $faseKeyById[$img->fenofase_id] ?? null;
            if (!$clave) continue;

            $path = $img->path ?? '';
            // Si el path no tiene carpeta, le anteponemos "flowers/{slug}/"
            if ($path !== '' && strpos($path, '/') === false) {
                $slug = $slugByFlorId[$img->flor_id] ?? null;
                if ($slug) {
                    $path = "flowers/{$slug}/{$path}";
                }
            }

            $imgByFlorAndFase[$img->flor_id][$clave] = $path; // path RELATIVO bajo storage/app/public
        }

        // =========================
        // Duraciones por flor y fase (días desde 'boton')
        // =========================
        $duraciones = DB::table('flor_fase_duraciones')
            ->select('flor_id', 'fase_clave', 'offset_dias')
            ->get();

        // Mapa: [flor_id][fase_clave] => offset_dias
        $durByFlorFase = [];
        foreach ($duraciones as $d) {
            $durByFlorFase[$d->flor_id][$d->fase_clave] = (int) $d->offset_dias;
        }

        // =========================
        // Perfiles informativos del Catálogo (SOLO si la tabla existe)
        // Selecciona dinámicamente lo que haya para no romper si faltan columnas
        // =========================
        $perfilesByFlorId = [];
        if (Schema::hasTable('flor_perfiles')) {
            // Columnas realmente existentes
            $existing = Schema::getColumnListing('flor_perfiles');
            $desired  = ['flor_id','nombre_cientifico','habitat','usos','descripcion','cover_path'];

            // Intersección (y nos aseguramos de incluir flor_id)
            $select = array_values(array_intersect($desired, $existing));
            if (!in_array('flor_id', $select, true)) {
                $select[] = 'flor_id';
            }

            $rows = DB::table('flor_perfiles')->select($select)->get();

            foreach ($rows as $p) {
                $perfilesByFlorId[$p->flor_id] = [
                    'flor_id'           => $p->flor_id,
                    'nombre_cientifico' => $p->nombre_cientifico ?? null,
                    'habitat'           => $p->habitat ?? null,
                    'usos'              => $p->usos ?? null,
                    'descripcion'       => $p->descripcion ?? null,
                    'cover_path'        => $p->cover_path ?? null,
                ];
            }
        }

        return view('zonificacion.show', [
            'apiario'           => $apiario,
            'especies'          => $especies,
            'fenofases'         => $fenofases,
            'imgByFlorAndFase'  => $imgByFlorAndFase,
            'durByFlorFase'     => $durByFlorFase,
            'perfilesByFlorId'  => $perfilesByFlorId, // para el tab "Catálogo de Flora"
        ]);
    }
}
