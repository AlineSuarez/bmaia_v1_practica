<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apiario;
use App\Models\Region;
use App\Models\Comuna;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class N8nWebhookController extends Controller
{
    /**
     * Endpoint para recibir instrucciones de n8n para crear apiario
     * POST /api/v1/n8n/create-apiario
     */
    public function createApiario(Request $request)
    {
        // 1. Validar firma HMAC
        if (!$this->validateHmacSignature($request)) {
            Log::warning('N8N: Firma HMAC inválida', [
                'ip' => $request->ip(),
                'signature' => $request->header('X-Signature')
            ]);
            return response()->json([
                'ok' => false,
                'error' => 'Firma no válida'
            ], 401);
        }

        // 2. Validar datos de entrada
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'nombre' => 'required|string|max:255',
            'tipo_apiario' => 'required|in:fijo,trashumante',
            'region' => 'nullable|string|max:255',
            'comuna' => 'nullable|string|max:255',
            'region_id' => 'nullable|exists:regiones,id',
            'comuna_id' => 'nullable|exists:comunas,id',
            'num_colmenas' => 'required|integer|min:1',
            'temporada_produccion' => 'nullable|string',
            'registro_sag' => 'nullable|string',
            'tipo_manejo' => 'nullable|string',
            'objetivo_produccion' => 'nullable|string',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'activo' => 'nullable|boolean',
            'es_temporal' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();

            // 3. Resolver región/comuna si vienen como nombres
            if (!isset($data['region_id']) && isset($data['region'])) {
                $region = Region::where('nombre', 'LIKE', '%' . $data['region'] . '%')->first();
                if ($region) {
                    $data['region_id'] = $region->id;
                } else {
                    Log::warning('N8N: Región no encontrada', ['region' => $data['region']]);
                }
            }

            if (!isset($data['comuna_id']) && isset($data['comuna']) && isset($data['region_id'])) {
                $comuna = Comuna::where('nombre', 'LIKE', '%' . $data['comuna'] . '%')
                    ->where('region_id', $data['region_id'])
                    ->first();
                if ($comuna) {
                    $data['comuna_id'] = $comuna->id;
                } else {
                    Log::warning('N8N: Comuna no encontrada', [
                        'comuna' => $data['comuna'],
                        'region_id' => $data['region_id']
                    ]);
                }
            }

            // 3.1 Si no viene lat/lon y la comuna tiene coordenadas en la BD, completarlas
            if (
                isset($data['comuna_id']) &&
                (!isset($data['latitud']) || !isset($data['longitud']))
            ) {
                $com = Comuna::find($data['comuna_id']);
                if ($com) {
                    $data['latitud'] = $com->lat;
                    $data['longitud'] = $com->lon;
                }
            }

            // 4. Normalizar tipo_manejo
            $manejoMap = [
                'orgánico' => 'Orgánico',
                'organico' => 'Orgánico',
                'convencional' => 'Convencional',
            ];
            if (isset($data['tipo_manejo'])) {
                $data['tipo_manejo'] = $manejoMap[strtolower($data['tipo_manejo'])] ?? 'Convencional';
            }

            // 5. Normalizar objetivo_produccion
            $objetivoMap = [
                'producción' => 'Producción',
                'produccion' => 'Producción',
                'material biológico' => 'Material biológico',
                'material biologico' => 'Material biológico',
                'polinización' => 'Polinización',
                'polinizacion' => 'Polinización',
                'otras' => 'Otras',
            ];
            if (isset($data['objetivo_produccion'])) {
                $data['objetivo_produccion'] = $objetivoMap[strtolower($data['objetivo_produccion'])] ?? 'Producción';
            }

            // 6. Crear el apiario
            $apiario = Apiario::create([
                'user_id' => $data['user_id'],
                'nombre' => $data['nombre'],
                'tipo_apiario' => $data['tipo_apiario'],
                'region_id' => $data['region_id'] ?? null,
                'comuna_id' => $data['comuna_id'] ?? null,
                'num_colmenas' => $data['num_colmenas'],
                'temporada_produccion' => $data['temporada_produccion'] ?? null,
                'registro_sag' => $data['registro_sag'] ?? null,
                'tipo_manejo' => $data['tipo_manejo'] ?? 'Convencional',
                'objetivo_produccion' => $data['objetivo_produccion'] ?? 'Producción',
                'latitud' => $data['latitud'] ?? null,
                'longitud' => $data['longitud'] ?? null,
                'activo' => $data['activo'] ?? true, // ⬅️ AGREGADO (valor por defecto true)
                'es_temporal' => $data['es_temporal'] ?? false, // ⬅️ AGREGADO (valor por defecto false)
            ]);

            // 7. Crear las colmenas del apiario (igual que en ApiarioController)
            for ($i = 1; $i <= $apiario->num_colmenas; $i++) {
                $codigo = url("/apiarios/{$apiario->id}/colmenas/{$i}");
                $apiario->colmenas()->create([
                    'nombre' => 'Colmena ' . $i,
                    'numero' => (string) $i,
                    'color_etiqueta' => 'Amarillo',
                    'codigo_qr' => $codigo,
                ]);
            }

            Log::info('N8N: Apiario creado exitosamente', [
                'apiario_id' => $apiario->id,
                'user_id' => $data['user_id'],
                'nombre' => $apiario->nombre,
                'activo' => $apiario->activo,
                'es_temporal' => $apiario->es_temporal,
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Apiario creado exitosamente',
                'data' => [
                    'id' => $apiario->id,
                    'nombre' => $apiario->nombre,
                    'tipo_apiario' => $apiario->tipo_apiario,
                    'num_colmenas' => $apiario->num_colmenas,
                    'region' => $apiario->region?->nombre,
                    'comuna' => $apiario->comuna?->nombre,
                    'activo' => $apiario->activo,
                    'es_temporal' => $apiario->es_temporal,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('N8N: Error al crear apiario', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'ok' => false,
                'error' => 'Error al crear el apiario',
                'message' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Validar firma HMAC de n8n
     */
    private function validateHmacSignature(Request $request): bool
    {
        $secret = config('services.n8n.hmac_secret');

        if (empty($secret)) {
            Log::warning('N8N: HMAC secret no configurado');
            return false;
        }

        $signature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');
        $body = $request->getContent();

        if (!$signature || !$timestamp) {
            Log::warning('N8N: Headers HMAC faltantes');
            return false;
        }

        // Verificar timestamp (evitar ataques de replay)
        if (abs(time() - $timestamp) > 300) { // 5 minutos
            Log::warning('N8N: Timestamp expirado', [
                'timestamp' => $timestamp,
                'diff' => abs(time() - $timestamp)
            ]);
            return false;
        }

        // Calcular firma esperada
        $expectedSignature = hash_hmac('sha256', $body, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Endpoint de prueba para verificar conectividad
     * GET /api/v1/n8n/ping
     */
    public function ping(Request $request)
    {
        return response()->json([
            'ok' => true,
            'message' => 'B-MaiA API respondiendo correctamente',
            'timestamp' => now()->toIso8601String()
        ]);
    }

    public function deleteApiario(Request $request)
    {
        // 1. Validar firma HMAC
        if (!$this->validateHmacSignature($request)) {
            return response()->json(['ok' => false, 'error' => 'Firma no válida'], 401);
        }

        // 2. Obtener datos
        $id     = $request->input('apiario_id');
        $nombre = $request->input('nombre');
        $userId = $request->input('user_id');

        if (!$id && !$nombre) {
            return response()->json([
                'ok'    => false,
                'error' => 'Debes enviar apiario_id o nombre'
            ], 422);
        }

        // 3. Buscar apiario por id o nombre, pero SIEMPRE del usuario
        $query = Apiario::where('user_id', $userId);

        if ($id) {
            $apiario = $query->where('id', $id)->first();
        } else {
            $apiario = $query->where('nombre', 'LIKE', "%$nombre%")->first();
        }

        if (!$apiario) {
            return response()->json([
                'ok' => false,
                'error' => 'Apiario no encontrado'
            ], 404);
        }

        // 4. ELIMINAR TODAS LAS COLMENAS DEL APIARIO
        // -----------------------------------------------
        foreach ($apiario->colmenas as $colmena) {
            $colmena->delete(); // usa soft delete si corresponde
        }

        if ($apiario->es_temporal == 1) {
            return response()->json([
                'ok' => false,
                'error' => 'Un apiario temporal no puede eliminarse. Solo puede retornarse/archivarse.'
            ], 403);
        }

        // 5. ELIMINAR APIARIO
        $apiario->delete();

        // 6. RESPUESTA
        return response()->json([
            'ok' => true,
            'message' => 'Apiario y sus colmenas fueron eliminados correctamente.',
            'deleted_apiario_id' => $apiario->id,
            'deleted_colmenas_count' => $apiario->colmenas()->withTrashed()->count()
        ]);
    }

    public function updateApiario(Request $request)
    {
        if (!$this->validateHmacSignature($request)) {
            return response()->json(['ok' => false, 'error' => 'Firma no válida'], 401);
        }

        $id     = $request->input('apiario_id');
        $nombre = $request->input('nombre');
        $campo  = $request->input('campo');
        $valor  = $request->input('valor');
        $userId = $request->input('user_id');

        if ((!$id && !$nombre) || !$campo || $valor === null) {
            return response()->json([
                'ok'    => false,
                'error' => 'Faltan datos: id/nombre, campo o valor'
            ], 422);
        }

        $query = Apiario::where('user_id', $userId);

        if ($id) {
            $apiario = $query->find($id);
        } else {
            $apiario = $query->where('nombre', $nombre)->first();
        }

        if (!$apiario) {
            return response()->json(['ok' => false, 'error' => 'Apiario no encontrado'], 404);
        }

        $editable = [
            'nombre',
            'num_colmenas',
            'temporada_produccion',
            'registro_sag',
            'tipo_manejo',
            'objetivo_produccion',
            'region',
            'comuna',
            'latitud',
            'longitud'
        ];

        if (!in_array($campo, $editable)) {
            return response()->json([
                'ok' => false,
                'error' => "El campo '$campo' no se puede editar"
            ], 400);
        }

        if ($campo === 'region') {
            $r = Region::where('nombre', 'LIKE', "%$valor%")->first();
            if (!$r) return response()->json(['ok'=>false,'error'=>'Región no encontrada'],404);
            $apiario->region_id = $r->id;
        }
        elseif ($campo === 'comuna') {
            $c = Comuna::where('nombre','LIKE',"%$valor%")
                    ->where('region_id',$apiario->region_id)
                    ->first();
            if (!$c) return response()->json(['ok'=>false,'error'=>'Comuna no encontrada'],404);
            $apiario->comuna_id = $c->id;
        }
        else {
            $apiario->$campo = $valor;
        }

        $apiario->save();

        if ($campo === 'num_colmenas') 
        {
            $nuevoTotal = (int) $valor;
            $actuales = $apiario->colmenas()->count();

            // SI SE AGREGAN COLMENAS
            if ($nuevoTotal > $actuales) {
                for ($i = $actuales + 1; $i <= $nuevoTotal; $i++) {
                    $codigo = url("/apiarios/{$apiario->id}/colmenas/{$i}");
                    $apiario->colmenas()->create([
                        'nombre'        => 'Colmena ' . $i,
                        'numero'        => (string) $i,
                        'color_etiqueta'=> 'Amarillo',
                        'codigo_qr'     => $codigo,
                    ]);
                }
            }

            // SI SE ELIMINAN COLMENAS
            if ($nuevoTotal < $actuales) {
                $apiario->colmenas()
                    ->where('numero', '>', $nuevoTotal)
                    ->delete(); // Soft delete (puedo cambiarlo a forceDelete si quieres)
            }
        }

        return response()->json([
            'ok'      => true,
            'message' => 'Apiario actualizado correctamente',
            'updated' => [
                'id'    => $apiario->id,
                'campo' => $campo,
                'valor' => $valor
            ]
        ]);
    }

    public function listApiarios(Request $request)
    {
        if (!$this->validateHmacSignature($request)) {
            return response()->json(['ok' => false, 'error' => 'Firma no válida'], 401);
        }

        $userId = $request->input('user_id');

        $apiarios = Apiario::where('user_id', $userId)
            ->with(['region','comuna'])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'ok'   => true,
            'count'=> $apiarios->count(),
            'data' => $apiarios->map(function ($a) {
                return [
                    'id'           => $a->id,
                    'nombre'       => $a->nombre,
                    'region'       => $a->region?->nombre,
                    'comuna'       => $a->comuna?->nombre,
                    'num_colmenas' => $a->activo ? $a->num_colmenas : ($a->colmenas_historicas ?? $a->num_colmenas),
                    'tipo_apiario'  => $a->tipo_apiario,
                    'es_temporal'   => $a->es_temporal,
                    'activo'        => $a->activo,
                ];
            })
        ]);
    }

}