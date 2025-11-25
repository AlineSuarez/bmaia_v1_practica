<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\RegionPerfil;
use App\Models\Comuna;
use App\Models\ComunaPerfil;
use Illuminate\Http\Request;

class RegionPerfilController extends Controller
{
    // GET JSON por slug de región (ej. 'los-lagos')
    public function showBySlug(string $slug)
    {
        $region = Region::where('slug', $slug)->firstOrFail();

        $perfil = RegionPerfil::firstOrCreate(
            ['region_id' => $region->id],
            [] // crea vacío si no existe
        );

        // Comunas de esa región + perfíl (si existe)
        $comunas = Comuna::where('region_id', $region->id)
            ->orderBy('nombre')
            ->get(['id','nombre']);

        $comunasConPerfil = $comunas->map(function ($c) {
            $cp = ComunaPerfil::where('comuna_id', $c->id)->first();
            return [
                'id'   => $c->id,
                'nombre' => $c->nombre,
                'perfil' => $cp ? [
                    'bosque_nativo_ha' => $cp->bosque_nativo_ha,
                    'plantaciones_forestales_ha' => $cp->plantaciones_forestales_ha,
                    'bosque_nativo_pct' => $cp->bosque_nativo_pct,
                    'plantaciones_forestales_pct' => $cp->plantaciones_forestales_pct,
                    'elevacion_promedio_m' => $cp->elevacion_promedio_m,
                    'precipitacion_anual_mm' => $cp->precipitacion_anual_mm,
                    'notas' => $cp->notas,
                ] : null,
            ];
        });

        return response()->json([
            'ok' => true,
            'region' => [
                'id' => $region->id,
                'nombre' => $region->nombre,
                'slug' => $region->slug,
            ],
            'perfil' => [
                'bosque_nativo_ha' => $perfil->bosque_nativo_ha,
                'plantaciones_forestales_ha' => $perfil->plantaciones_forestales_ha,
                'bosque_nativo_pct' => $perfil->bosque_nativo_pct,
                'plantaciones_forestales_pct' => $perfil->plantaciones_forestales_pct,
                'amenaza_actual' => $perfil->amenaza_actual,
                'amenaza_futuro' => $perfil->amenaza_futuro,
                'exposicion' => $perfil->exposicion,
                'sensibilidad' => $perfil->sensibilidad,
                'riesgo_actual' => $perfil->riesgo_actual,
                'riesgo_futuro' => $perfil->riesgo_futuro,
                'resumen' => $perfil->resumen,
                'updated_at' => optional($perfil->updated_at)->toDateTimeString(),
            ],
            'comunas' => $comunasConPerfil,
        ]);
    }

    // POST/PUT para guardar/actualizar perfil de región
    public function upsert(Request $req, string $slug)
    {
        $region = Region::where('slug', $slug)->firstOrFail();

        $data = $req->validate([
            'bosque_nativo_ha' => 'nullable|numeric',
            'plantaciones_forestales_ha' => 'nullable|numeric',
            'bosque_nativo_pct' => 'nullable|numeric',
            'plantaciones_forestales_pct' => 'nullable|numeric',
            'amenaza_actual' => 'nullable|numeric',
            'amenaza_futuro' => 'nullable|numeric',
            'exposicion' => 'nullable|numeric',
            'sensibilidad' => 'nullable|numeric',
            'riesgo_actual' => 'nullable|numeric',
            'riesgo_futuro' => 'nullable|numeric',
            'resumen' => 'nullable|string',
        ]);

        $perfil = RegionPerfil::updateOrCreate(['region_id'=>$region->id], $data);

        return response()->json([
            'ok'=>true,
            'perfil_id'=>$perfil->id,
        ]);
    }
}
