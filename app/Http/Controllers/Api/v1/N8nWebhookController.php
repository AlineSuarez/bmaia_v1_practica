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
}