<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Region;
use App\Models\Comuna;
use App\Models\RegionPerfil;
use App\Models\ComunaPerfil;

class MapaExploradorController extends Controller
{
    /**
     * Vista Explorador del mapa (inyecta listado de regiones).
     */
    public function index()
    {
        // Tomamos id, nombre y una clave legible (slug UI) priorizando abreviatura
        $regiones = Region::query()
            ->select([
                'id',
                'nombre',
                DB::raw("COALESCE(NULLIF(TRIM(abreviatura),''), REPLACE(LOWER(nombre),' ', '-')) as key_ui")
            ])
            ->orderBy('nombre')
            ->get()
            ->map(function ($r) {
                return [
                    'id'      => (int) $r->id,
                    'nombre'  => $r->nombre,
                    'slug'    => strtoupper($r->key_ui), // para el selector (REGIÓN: BIOBÍO, RM, etc.)
                    'key'     => $r->key_ui,            // por si necesitas minúsculas
                ];
            });

        return view('hoja_ruta.explorador', compact('regiones'));
    }

    /**
     * Perfil de una región por slug (abreviatura o slug del nombre).
     * GET /api/mapa/region/{slug}/perfil
     */
    public function regionPerfil(string $slug)
    {
        $region = $this->findRegionBySlug($slug);
        if (!$region) {
            return response()->json([
                'ok' => false,
                'error' => "No se encontró la región '{$slug}'."
            ], 404);
        }

        $perfil = RegionPerfil::where('region_id', $region->id)->first();

        return response()->json([
            'ok' => true,
            'region' => [
                'id'          => (int) $region->id,
                'nombre'      => $region->nombre,
                'abreviatura' => $region->abreviatura,
                'slug'        => $this->regionSlug($region),
            ],
            'perfil' => $perfil ? $this->mapRegionPerfil($perfil) : null,
        ]);
    }

    /**
     * Comunas + perfiles para una región.
     * GET /api/mapa/region/{slug}/comunas
     */
    public function regionComunas(string $slug)
    {
        $region = $this->findRegionBySlug($slug);
        if (!$region) {
            return response()->json([
                'ok' => false,
                'error' => "No se encontró la región '{$slug}'."
            ], 404);
        }

        // Comunas de la región
        $comunas = Comuna::query()
            ->where('region_id', $region->id)
            ->select(['id', 'nombre', 'cod_externo'])
            ->orderBy('nombre')
            ->get();

        // Traemos todos los perfiles de una
        $perfiles = ComunaPerfil::whereIn('comuna_id', $comunas->pluck('id'))
            ->get()
            ->keyBy('comuna_id');

        $data = $comunas->map(function ($c) use ($perfiles) {
            $perfil = $perfiles->get($c->id);

            return [
                'id'          => (int) $c->id,
                'nombre'      => $c->nombre,
                'cod_externo' => $c->cod_externo,
                'perfil'      => $perfil ? $this->mapComunaPerfil($perfil) : null,
            ];
        });

        return response()->json([
            'ok' => true,
            'region' => [
                'id'          => (int) $region->id,
                'nombre'      => $region->nombre,
                'abreviatura' => $region->abreviatura,
                'slug'        => $this->regionSlug($region),
            ],
            'comunas' => $data,
        ]);
    }

    /**
     * Perfil puntual de una comuna por ID.
     * GET /api/mapa/comuna/{id}/perfil
     */
    public function comunaPerfil(int $id)
    {
        $comuna = Comuna::find($id);

        if (!$comuna) {
            return response()->json([
                'ok' => false,
                'error' => "No se encontró la comuna id={$id}."
            ], 404);
        }

        $perfil = ComunaPerfil::where('comuna_id', $id)->first();

        return response()->json([
            'ok' => true,
            'comuna' => [
                'id'          => (int) $comuna->id,
                'nombre'      => $comuna->nombre,
                'cod_externo' => $comuna->cod_externo,
                'region_id'   => (int) $comuna->region_id,
            ],
            'perfil' => $perfil ? $this->mapComunaPerfil($perfil) : null,
        ]);
    }

    /* ============================
       Helpers
       ============================ */

    /**
     * Busca una región por abreviatura (case-insensitive) o por slug(nombre).
     */
    private function findRegionBySlug(string $slug): ?Region
    {
        $slugUpper = strtoupper($slug);
        $slugName  = Str::slug($slug);

        // 1) Coincidencia por abreviatura exacta (RM, BIOBIO, AYSEN, etc.)
        $q = Region::query()
            ->whereRaw('UPPER(COALESCE(abreviatura,"")) = ?', [$slugUpper]);

        $region = $q->first();

        if (!$region) {
            // 2) Fallback: slug del nombre (ej. "biobio" ~ "biobío")
            $region = Region::get()
                ->first(function ($r) use ($slugName) {
                    return Str::slug($r->nombre) === $slugName;
                });
        }

        return $region ?: null;
    }

    /**
     * Normaliza/convierte a números los campos del perfil de región.
     */
    private function mapRegionPerfil(RegionPerfil $p): array
    {
        return [
            'id'                       => (int) $p->id,
            'region_id'                => (int) $p->region_id,
            'slug'                     => $p->slug,
            'nombre'                   => $p->nombre,
            'resumen'                  => $p->resumen,
            'bucket'                   => $p->bucket,
            'bosque_nativo_ha'         => $this->f($p->bosque_nativo_ha),
            'plantaciones_forestales_ha'  => $this->f($p->plantaciones_forestales_ha),
            'bosque_nativo_pct'        => $this->f($p->bosque_nativo_pct),
            'plantaciones_forestales_pct' => $this->f($p->plantaciones_forestales_pct),
            'amenaza_actual'           => $this->f($p->amenaza_actual),
            'amenaza_futuro'           => $this->f($p->amenaza_futuro),
            'exposicion'               => $this->f($p->exposicion),
            'sensibilidad'             => $this->f($p->sensibilidad),
            'riesgo_actual'            => $this->f($p->riesgo_actual),
            'riesgo_futuro'            => $this->f($p->riesgo_futuro),
        ];
    }

    /**
     * Normaliza/convierte a números los campos del perfil de comuna.
     */
    private function mapComunaPerfil(ComunaPerfil $p): array
    {
        return [
            'id'                       => (int) $p->id,
            'comuna_id'                => (int) $p->comuna_id,
            'slug'                     => $p->slug,
            'nombre'                   => $p->nombre,
            'cod_externo'              => $p->cod_externo,
            'bosques_nativos_ha'       => $this->f($p->bosques_nativos_ha ?? $p->bosque_nativo_ha ?? null),
            'plantaciones_ha'          => $this->f($p->plantaciones_ha),
            'pct_bosques'              => $this->f($p->pct_bosques ?? $p->bosque_nativo_pct ?? null),
            'pct_plantaciones'         => $this->f($p->pct_plantaciones ?? $p->plantaciones_forestales_pct ?? null),
            'notas'                    => $p->notas,
        ];
    }

    /**
     * Convierte valores numéricos/null de forma segura.
     */
    private function f($val): ?float
    {
        if ($val === null || $val === '') return null;
        return (float) $val;
    }

    private function regionSlug(Region $r): string
    {
        return $r->abreviatura ? strtoupper($r->abreviatura) : Str::slug($r->nombre);
    }
}
