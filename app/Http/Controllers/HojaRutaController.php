<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;    // <=== NUEVO

// ===== Modelos Hoja de Ruta (Catálogo de flora) =====
use App\Models\Fenofase;           // tabla: fenofases
use App\Models\Flor;               // tabla: flores
use App\Models\FlorImagen;         // tabla: flor_imagenes
use App\Models\FlorPerfil;         // tabla: flor_perfiles
use App\Models\FlorFaseDuracion;   // tabla: flor_fase_duraciones

// ===== (Opcional) modelos para el mapa =====
use App\Models\RegionMaps;         // tabla: region_maps (si lo tienes creado)
use App\Models\ComunaMaps;         // tabla: comuna_maps (si lo tienes creado)

class HojaRutaController extends Controller
{
    /**
     * Catálogo de Flora (vista de Hoja de Ruta)
     * GET /hoja-de-ruta/catalogo-flora  -> name('hojaruta.catalogo')
     * Vista: resources/views/hoja_ruta/catalogo.blade.php
     */
    public function catalogoFlora(Request $request)
    {
        // 1) Fenofases en orden lógico para el JS
        $fenofases = Fenofase::select('clave', 'nombre')
            ->orderByRaw("FIELD(clave,'boton','inicio','plena','terminal')")
            ->get();

        // 2) Especies (flores)
        $especies = Flor::select('id', 'nombre', 'slug')
            ->orderBy('nombre', 'asc')
            ->get();

        // === helpers para leer columnas con nombres distintos ===
        $getPhaseKey = function ($row) {
            // intenta distintas variantes de nombre de columna
            $attrs = $row->getAttributes();
            if (array_key_exists('fenofase_clave', $attrs))  return $row->fenofase_clave;
            if (array_key_exists('fase_clave', $attrs))      return $row->fase_clave;
            if (array_key_exists('fenofase', $attrs))        return $row->fenofase;
            if (array_key_exists('fase', $attrs))            return $row->fase;

            foreach (array_keys($attrs) as $k) {
                $lk = strtolower($k);
                if (in_array($lk, ['fenofase_clave','fase_clave','fenofase','fase'])) {
                    return $attrs[$k];
                }
            }
            return null;
        };

        // 3) Imágenes por especie y fenofase: $imgByFlorAndFase[flor_id][fase] = 'path'
        $imgByFlorAndFase = [];
        FlorImagen::select('*')->get()->each(function ($img) use (&$imgByFlorAndFase, $getPhaseKey) {
            $fase = $getPhaseKey($img);
            if (!$fase) { return; }
            // determina columna path/imagen/url
            $attrs = $img->getAttributes();
            $path = $attrs['path'] ?? ($attrs['imagen'] ?? ($attrs['url'] ?? null));
            if (!$path) { return; }
            $imgByFlorAndFase[$img->flor_id][$fase] = $path;
        });

        // 4) Offsets/duraciones por especie/fase (timeline fenológico)
        $durByFlorFase = [];
        FlorFaseDuracion::select('*')->get()->each(function ($d) use (&$durByFlorFase, $getPhaseKey) {
            $fase = $getPhaseKey($d);
            if (!$fase) { return; }
            $offset = (int) ($d->offset_dias ?? $d->offset ?? 0);
            $durByFlorFase[$d->flor_id][$fase] = $offset;
        });

        // 5) Perfiles enriquecidos (tarjetas + modal)
        $perfilesByFlorId = [];
        FlorPerfil::select('*')->get()->each(function ($p) use (&$perfilesByFlorId) {
            $perfilesByFlorId[$p->flor_id] = [
                'nombre_comun_alt' => $p->nombre_comun_alt ?? null,
                'resumen'          => $p->resumen ?? null,
                'descripcion'      => $p->descripcion ?? null,
                'habitat'          => $p->habitat ?? null,
                'distribucion'     => $p->distribucion ?? null,
                'nectar_score'     => $p->nectar_score ?? null,
                'polen_score'      => $p->polen_score ?? null,
                'usos'             => $p->usos ?? null,
                'fuente'           => $p->fuente ?? null,
                'enlace'           => $p->enlace ?? null,
                'cover_path'       => $p->cover_path ?? null,
            ];
        });

        return view('hoja_ruta.catalogo', compact(
            'fenofases',
            'especies',
            'imgByFlorAndFase',
            'durByFlorFase',
            'perfilesByFlorId'
        ));
    }

    /**
     * Explorador de Zonas (vista del mapa SVG)
     * GET /hoja-de-ruta/explorador
     */
    public function explorador()
    {
        return view('hoja_ruta.explorador');
    }

    /**
     * Endpoint para obtener región + comunas desde las tablas region_maps / comuna_maps.
     * GET /hoja-de-ruta/api/region/{iso}
     */
    public function apiRegion(string $iso)
    {
        $iso = strtoupper($iso); // por si llega en minúsculas

        // 1) Buscar región en region_maps
        $region = DB::table('region_maps')
            ->where('iso_code', $iso)
            ->select('id', 'nombre', 'iso_code')
            ->first();

        if (!$region) {
            return response()->json([
                'error'   => 'Región no encontrada para el código ISO: ' . $iso,
                'region'  => null,
                'comunas' => [],
            ], 404);
        }

        // 2) Intentar cargar comunas. Si algo explota, devolvemos arreglo vacío pero 200.
        $comunas = [];

        try {
            if (DB::getSchemaBuilder()->hasTable('comuna_maps')) {
                $comunas = DB::table('comuna_maps')
                    ->where('region_maps_id', $region->id)   // ajusta este nombre si tu FK es distinto
                    ->orderBy('nombre', 'asc')
                    ->get(['id', 'nombre'])
                    ->map(function ($c) {
                        return [
                            'id'     => $c->id,
                            'nombre' => $c->nombre,
                        ];
                    })
                    ->toArray();
            }
        } catch (\Throwable $e) {
            // No rompemos la API, solo dejamos comunas vacío y logueamos
            \Log::warning('Error cargando comunas para región ' . $iso . ': ' . $e->getMessage());
            $comunas = [];
        }

        return response()->json([
            'region' => [
                'id'       => $region->id,
                'nombre'   => $region->nombre,
                'iso_code' => $region->iso_code,
            ],
            'comunas' => $comunas,
        ]);
    }
}
