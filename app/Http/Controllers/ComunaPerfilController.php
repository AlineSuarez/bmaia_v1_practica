<?php

namespace App\Http\Controllers;

use App\Models\Comuna;
use App\Models\ComunaPerfil;
use Illuminate\Http\Request;

class ComunaPerfilController extends Controller
{
    // GET de una comuna
    public function show(int $comunaId)
    {
        $comuna = Comuna::findOrFail($comunaId);
        $perfil = ComunaPerfil::firstOrCreate(['comuna_id'=>$comuna->id], []);

        return response()->json([
            'ok'=>true,
            'comuna'=>[
                'id'=>$comuna->id,
                'nombre'=>$comuna->nombre,
            ],
            'perfil'=>$perfil,
        ]);
    }

    // POST/PUT upsert
    public function upsert(Request $req, int $comunaId)
    {
        $comuna = Comuna::findOrFail($comunaId);

        $data = $req->validate([
            'bosque_nativo_ha' => 'nullable|numeric',
            'plantaciones_forestales_ha' => 'nullable|numeric',
            'bosque_nativo_pct' => 'nullable|numeric',
            'plantaciones_forestales_pct' => 'nullable|numeric',
            'elevacion_promedio_m' => 'nullable|numeric',
            'precipitacion_anual_mm' => 'nullable|numeric',
            'notas' => 'nullable|string',
        ]);

        $perfil = ComunaPerfil::updateOrCreate(['comuna_id'=>$comuna->id], $data);

        return response()->json(['ok'=>true,'perfil_id'=>$perfil->id]);
    }
}
