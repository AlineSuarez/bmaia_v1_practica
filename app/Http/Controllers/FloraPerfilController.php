<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class FloraPerfilController extends Controller
{
    /**
     * Muestra el perfil de flora (para el modal "Ver más").
     *
     * @param  int  $flor  ID de la flor (flor_id)
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $flor, Request $request): JsonResponse
    {
        try {
            // Perfil (tabla: flor_perfiles)
            $perfil = DB::table('flor_perfiles')
                ->where('flor_id', $flor)
                ->first();

            // Datos base de la flor (si existe tabla "flores" con nombre científico/común)
            $florBase = DB::table('flores')
                ->where('id', $flor)
                ->first();

            // Imagen principal (intenta distintas columnas comunes)
            $imagenRow = DB::table('flor_imagenes')
                ->where('flor_id', $flor)
                ->orderByDesc(DB::raw("CASE WHEN (is_primary = 1 OR principal = 1) THEN 1 ELSE 0 END"))
                ->orderByDesc('id')
                ->first();

            $imageUrl = null;
            if ($imagenRow) {
                // Detecta el nombre de columna disponible
                foreach (['url', 'ruta', 'path', 'imagen', 'image'] as $c) {
                    if (isset($imagenRow->{$c}) && !empty($imagenRow->{$c})) {
                        $imageUrl = $imagenRow->{$c};
                        break;
                    }
                }
            }

            return response()->json([
                'ok' => true,
                'data' => [
                    'flor_id' => $flor,
                    'flor' => $florBase ? [
                        'id' => $florBase->id,
                        'nombre_cientifico' => $florBase->nombre_cientifico ?? null,
                        'nombre_comun'       => $florBase->nombre_comun ?? null,
                    ] : null,
                    'perfil' => $perfil ? [
                        'id' => $perfil->id,
                        'nombre_comun_alt' => $perfil->nombre_comun_alt,
                        'resumen'          => $perfil->resumen,
                        'descripcion'      => $perfil->descripcion,
                        'habitat'          => $perfil->habitat,
                        'distribucion'     => $perfil->distribucion,
                        'nectar_score'     => $perfil->nectar_score,
                        'polen_score'      => $perfil->polen_score,
                        'usos'             => $perfil->usos,
                        'fuente'           => $perfil->fuente,
                        'enlace'           => $perfil->enlace,
                    ] : null,
                    'image_url' => $imageUrl,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Error FloraPerfil@show', ['flor_id' => $flor, 'e' => $e]);
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo obtener la información de la flor.',
            ], 500);
        }
    }

    /**
     * Actualiza/crea el perfil de flora (desde el modal).
     * Acepta campos editables del registro de flor_perfiles.
     *
     * @param  int  $flor  ID de la flor (flor_id)
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(int $flor, Request $request): JsonResponse
    {
        // Validación de los campos editables
        $data = $request->validate([
            'nombre_comun_alt' => ['nullable', 'string', 'max:255'],
            'resumen'          => ['nullable', 'string', 'max:2000'],
            'descripcion'      => ['nullable', 'string', 'max:10000'],
            'habitat'          => ['nullable', 'string', 'max:2000'],
            'distribucion'     => ['nullable', 'string', 'max:2000'],
            'nectar_score'     => ['nullable', 'integer', 'min:0', 'max:10'],
            'polen_score'      => ['nullable', 'integer', 'min:0', 'max:10'],
            'usos'             => ['nullable', 'string', 'max:2000'],
            'fuente'           => ['nullable', 'string', 'max:500'],
            'enlace'           => ['nullable', 'url', 'max:1000'],
        ]);

        try {
            $now = now();

            // ¿Existe ya un perfil para esta flor?
            $existing = DB::table('flor_perfiles')->where('flor_id', $flor)->first();

            if ($existing) {
                // UPDATE
                DB::table('flor_perfiles')
                    ->where('id', $existing->id)
                    ->update(array_merge($data, [
                        'updated_at' => $now,
                    ]));
                $id = $existing->id;
            } else {
                // INSERT
                $id = DB::table('flor_perfiles')->insertGetId(array_merge($data, [
                    'flor_id'    => $flor,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }

            // Respuesta con el registro actualizado
            $perfil = DB::table('flor_perfiles')->where('id', $id)->first();

            return response()->json([
                'ok' => true,
                'message' => 'Perfil de flora guardado correctamente.',
                'perfil' => $perfil,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error FloraPerfil@update', ['flor_id' => $flor, 'e' => $e, 'payload' => $request->all()]);
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo guardar el perfil de la flor.',
            ], 500);
        }
    }
}
