<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\SyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncController extends Controller
{
    /**
     * Aplica operaciones del outbox del móvil (idempotente).
     * Body:
     * {
     *   "items": [
     *     {"uuid":"...","entity":"apiario","op":"update","payload":{...},"updated_at":1736550}
     *   ],
     *   "last_sync_at": 1736500000000
     * }
     */
    public function syncData(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.uuid'       => 'required|string',
            'items.*.entity'     => 'required|string|in:apiario,colmena,visita',
            'items.*.op'         => 'required|string|in:create,update,delete',
            'items.*.payload'    => 'nullable|array',
            'items.*.updated_at' => 'nullable|numeric',
            'last_sync_at'       => 'nullable|numeric',
        ]);

        $userId   = $request->user()->id;
        $accepted = [];
        $sync     = new SyncService(); // Opción A: sin dependencias

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $uuid   = $item['uuid'];
                $entity = $item['entity'];
                $op     = $item['op'];
                $payload= $item['payload'] ?? [];

                // Idempotencia
                if ($sync->alreadyApplied($uuid)) {
                    $accepted[] = ['uuid' => $uuid, 'status' => 'duplicate'];
                    continue;
                }

                // Aplica la operación (placeholder, reemplaza con tus modelos reales)
                $result = $this->applyEntityOp($userId, $entity, $op, $payload);

                // Registra log
                $sync->logApply(
                    userId:  $userId,
                    uuid:    $uuid,
                    entity:  $entity,
                    op:      $op,
                    meta:    ['result' => $result],
                    appliedAt: now()
                );

                $accepted[] = ['uuid' => $uuid, 'status' => 'ok'];
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => [
                    'code'    => 'SYNC_FAILED',
                    'message' => $e->getMessage(),
                ]
            ], 500);
        }

        // Opcional: devolver cambios del servidor desde last_sync_at
        $serverChanges = []; // TODO: implementar si necesitas pull incremental

        return response()->json([
            'accepted'     => $accepted,
            'server_time'  => now()->toIso8601String(),
            'server_changes' => $serverChanges,
        ]);
    }

    /**
     * Estado básico de sincronización (placeholder).
     */
    public function syncStatus(Request $request)
    {
        $userId = $request->user()->id;

        $count = DB::table('sync_logs')->where('user_id', $userId)->count();

        return response()->json([
            'entries' => $count,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * Devuelve cambios desde 'since' (epoch ms) – placeholder.
     * GET /changes?since=1736500000000&entities=apiario,colmena,visita
     */
    public function changes(Request $request)
    {
        $since    = (int) $request->query('since', 0);
        $entities = array_filter(explode(',', (string) $request->query('entities', '')));

        // TODO: Implementa consulta real a tus tablas por updated_at > $since
        // y filtra por $entities si viene el parámetro.
        return response()->json(['data' => []]);
    }

    /**
     * SSE stream (placeholder: no implementado).
     */
    public function stream(Request $request)
    {
        return response('Not Implemented', 501);
    }

    /**
     * Aplica una operación a la entidad (placeholder).
     * Aquí debes llamar a tus modelos reales: Apiario, Colmena, Visita.
     */
    private function applyEntityOp(int $userId, string $entity, string $op, array $payload): array
    {
        // IMPORTANTE: reemplaza por tus modelos/validaciones reales.
        // Ejemplo genérico:
        $id = $payload['id'] ?? null;

        switch ($entity) {
            case 'apiario':
                // switch $op (create/update/delete) y usa tus modelos
                return ['entity' => 'apiario', 'op' => $op, 'id' => $id ?: Str::uuid()];
            case 'colmena':
                return ['entity' => 'colmena', 'op' => $op, 'id' => $id ?: Str::uuid()];
            case 'visita':
                return ['entity' => 'visita', 'op' => $op, 'id' => $id ?: Str::uuid()];
            default:
                throw new \InvalidArgumentException("Entidad no soportada: $entity");
        }
    }
}
