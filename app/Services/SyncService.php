<?php
namespace App\Services;

use App\Models\SyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SyncService
{
    /** @var array<string, callable> */
    private array $handlers;

    public function __construct()
    {
        // Inicializar handlers para cada entidad
        $this->handlers = [
            'apiario' => fn (string $op, array $p, int $userId) => $this->handleApiario($op, $p, $userId),
            'colmena' => fn (string $op, array $p, int $userId) => $this->handleColmena($op, $p, $userId),
            'visita'  => fn (string $op, array $p, int $userId) => $this->handleVisita($op, $p, $userId),
        ];

    }

    /**
     * Verifica si un uuid ya fue procesado (idempotencia).
     */
    public function alreadyApplied(string $uuid): bool
    {
        return SyncLog::where('uuid', $uuid)->exists();
    }

    /**
     * Registra la aplicación de una operación de /sync.
     */
    public function logApply(
        int $userId,
        string $uuid,
        string $entity,
        string $op,
        array $meta = [],
        ?Carbon $appliedAt = null
    ): SyncLog {
        return SyncLog::create([
            'user_id'    => $userId,
            'uuid'       => $uuid,
            'entity'     => $entity,
            'op'         => $op,
            'meta'       => $meta,
            'applied_at' => $appliedAt ?: now(),
        ]);
    }

    public function applyBatch(array $items, int $userId): array
    {
        $accepted = [];
        DB::transaction(function () use ($items, $userId, &$accepted) {
            foreach ($items as $i) {
                $uuid    = $i['uuid'];
                $entity  = $i['entity'];
                $op      = $i['op'];
                $payload = $i['payload'];
                $clientUpdatedAt = Carbon::createFromTimestampMs($i['updated_at']);

                if (SyncLog::where('uuid', $uuid)->exists()) {
                    $accepted[] = ['uuid'=>$uuid,'status'=>'duplicate'];
                    continue;
                }
                if (!isset($this->handlers[$entity])) {
                    $accepted[] = ['uuid'=>$uuid,'status'=>'unsupported_entity'];
                    continue;
                }

                $result = ($this->handlers[$entity])($op, $payload, $userId);

                SyncLog::create([
                    'uuid'       => $uuid,
                    'entity'     => $entity,
                    'op'         => $op,
                    'user_id'    => $userId,
                    'applied_at' => now(),
                    'meta'       => ['client_updated_at'=>$clientUpdatedAt->toISOString(),'result'=>$result],
                ]);

                $accepted[] = ['uuid'=>$uuid,'status'=>$result['status'] ?? 'ok'];
            }
        });
        return $accepted;
    }

    /** ------------- HANDLERS (ajusta campos a tus modelos) ------------- */

    private function handleApiario(string $op, array $p, int $userId): array
    {
        $id = $p['id'] ?? null;
        $m  = $id ? \App\Models\Apiario::find($id) : null;

        if ($op === 'delete') { if ($m) $m->delete(); return ['status'=>'ok']; }

        if (!$m) { $m = new \App\Models\Apiario(); $m->user_id = $userId; }
        else {
            $clientTs = Carbon::createFromTimestampMs($p['updated_at'] ?? now()->getTimestampMs());
            if ($m->updated_at && $m->updated_at->greaterThan($clientTs)) {
                return ['status'=>'conflict','server_version'=>$m->toArray()];
            }
        }

        $m->nombre    = $p['nombre']    ?? $m->nombre;
        $m->ubicacion = $p['ubicacion'] ?? $m->ubicacion;
        // otros campos: region_id, comuna_id, activo, etc.
        $m->save();

        event(new \App\Events\ApiarioActualizado($m->toArray(), $userId));
        return ['status'=>'ok','server_version'=>$m->fresh()->toArray()];
    }

    private function handleColmena(string $op, array $p, int $userId): array
    {
        $id = $p['id'] ?? null;
        $m  = $id ? \App\Models\Colmena::find($id) : null;

        if ($op === 'delete') { if ($m) $m->delete(); return ['status'=>'ok']; }

        if (!$m) { $m = new \App\Models\Colmena(); $m->apiario_id = $p['apiario_id'] ?? null; $m->user_id = $userId; }
        else {
            $clientTs = Carbon::createFromTimestampMs($p['updated_at'] ?? now()->getTimestampMs());
            if ($m->updated_at && $m->updated_at->greaterThan($clientTs)) {
                return ['status'=>'conflict','server_version'=>$m->toArray()];
            }
        }

        $m->nombre         = $p['nombre'] ?? $m->nombre;
        $m->numero         = $p['numero'] ?? $m->numero;
        $m->color_etiqueta = $p['color_etiqueta'] ?? $m->color_etiqueta;
        $m->save();

        event(new \App\Events\ColmenaActualizada($m->toArray(), $userId));
        return ['status'=>'ok','server_version'=>$m->fresh()->toArray()];
    }

    private function handleVisita(string $op, array $p, int $userId): array
    {
        $id = $p['id'] ?? null;
        $m  = $id ? \App\Models\Visita::find($id) : null;

        if ($op === 'delete') { if ($m) $m->delete(); return ['status'=>'ok']; }

        if (!$m) { $m = new \App\Models\Visita(); $m->user_id = $userId; }
        else {
            $clientTs = Carbon::createFromTimestampMs($p['updated_at'] ?? now()->getTimestampMs());
            if ($m->updated_at && $m->updated_at->greaterThan($clientTs)) {
                return ['status'=>'conflict','server_version'=>$m->toArray()];
            }
        }

        $m->apiario_id   = $p['apiario_id']   ?? $m->apiario_id;
        $m->colmena_id   = $p['colmena_id']   ?? $m->colmena_id;
        $m->tipo         = $p['tipo']         ?? $m->tipo; // 'general' | 'inspeccion' | ...
        $m->observaciones= $p['observaciones']?? $m->observaciones;
        $m->fecha        = isset($p['fecha']) ? \Carbon\Carbon::parse($p['fecha']) : ($m->fecha ?? now());
        $m->save();

        event(new \App\Events\VisitaActualizada($m->toArray(), $userId));
        return ['status'=>'ok','server_version'=>$m->fresh()->toArray()];
    }

    /** ------------- Cambios del servidor desde last_sync_at ------------- */

    public function serverChanges(?int $lastSyncEpochMs, int $userId): array
    {
        if (!$lastSyncEpochMs) return [];
        $since = Carbon::createFromTimestampMs($lastSyncEpochMs);
        $changes = [];

        $apiarios = \App\Models\Apiario::query()
            ->where('user_id',$userId)->where('updated_at','>',$since)->limit(500)->get();
        foreach ($apiarios as $a) {
            $changes[] = [
                'entity'=>'apiario','op'=>'upsert','payload'=>$a->toArray(),
                'updated_at'=>$a->updated_at?->getTimestampMs(),
            ];
        }

        $colmenas = \App\Models\Colmena::query()
            ->where('user_id',$userId)->where('updated_at','>',$since)->limit(500)->get();
        foreach ($colmenas as $c) {
            $changes[] = [
                'entity'=>'colmena','op'=>'upsert','payload'=>$c->toArray(),
                'updated_at'=>$c->updated_at?->getTimestampMs(),
            ];
        }

        $visitas = \App\Models\Visita::query()
            ->where('user_id',$userId)->where('updated_at','>',$since)->limit(500)->get();
        foreach ($visitas as $v) {
            $changes[] = [
                'entity'=>'visita','op'=>'upsert','payload'=>$v->toArray(),
                'updated_at'=>$v->updated_at?->getTimestampMs(),
            ];
        }

        return $changes;
    }
}
