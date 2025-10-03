<?php

namespace App\Services;

use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\MovimientoColmena;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApiarioService
{
    public function crear(array $data): Apiario
    {
        return DB::transaction(function () use ($data) {
            $userId = $data['user_id'] ?? Auth::id();
            if (!$userId) {
                abort(401, 'No autenticado');
            }

            $n = (int)($data['colmenas_iniciales'] ?? 0);

            $apiario = Apiario::create([
                'user_id'            => $userId,
                'nombre'             => $data['nombre'] ?? null,
                'tipo_apiario'       => $data['tipo_apiario'] ?? null, // 👈 usar tu campo
                'region_id'          => $data['region_id'] ?? null,
                'comuna_id'          => $data['comuna_id'] ?? null,
                'latitud'            => $data['latitud']   ?? null,
                'longitud'           => $data['longitud']  ?? null,
                'activo'             => $data['activo']    ?? true,
                'num_colmenas'       => $n, // si tu columna existe, dejamos el conteo
            ]);

            // Si quieres crear físicamente las colmenas:
            if ($n > 0) {
                $payloads = [];
                for ($i = 0; $i < $n; $i++) {
                    $payloads[] = ['user_id' => $userId];
                }
                $apiario->colmenas()->createMany($payloads);
            }

            return $apiario;
        });
    }

    public function actualizar(Apiario $apiario, array $data): Apiario
    {
        $apiario->fill([
            'nombre'       => $data['nombre']       ?? $apiario->nombre,
            'tipo_apiario' => $data['tipo_apiario'] ?? $apiario->tipo_apiario,
            'region_id'    => $data['region_id']    ?? $apiario->region_id,
            'comuna_id'    => $data['comuna_id']    ?? $apiario->comuna_id,
            'latitud'      => $data['latitud']      ?? $apiario->latitud,
            'longitud'     => $data['longitud']     ?? $apiario->longitud,
        ])->save();

        return $apiario->refresh();
    }

    /** Traslado: crea temporal y mueve colmenas */
    public function traslado(array $data): Apiario
    {
        return DB::transaction(function () use ($data) {
            $tmp = Apiario::create([
                'nombre'    => $data['nombre_temporal'],
                'tipo'      => 'temporal',
                'region_id' => $data['region_id'] ?? null,
                'comuna_id' => $data['comuna_id'] ?? null,
                'activo'    => true,
            ]);

            foreach ($data['colmenas'] as $colmenaId) {
                $col = Colmena::lockForUpdate()->findOrFail($colmenaId);
                MovimientoColmena::create([
                    'colmena_id'         => $col->id,
                    'apiario_origen_id'  => $data['apiario_origen_id'],
                    'apiario_destino_id' => $tmp->id,
                    'fecha_inicio_mov'   => $data['fecha_inicio'],
                    'fecha_termino_mov'  => $data['fecha_termino'] ?? null,
                    'motivo'             => $data['motivo'] ?? null,
                    'tipo_movimiento'    => 'traslado',
                    'transportista'      => $data['transportista'] ?? null,
                    'vehiculo'           => $data['vehiculo'] ?? null,
                    'observaciones'      => $data['observaciones'] ?? null,
                ]);
                $col->update(['apiario_id' => $tmp->id]);
            }
            return $tmp->fresh();
        });
    }

    /** Retorno: devuelve colmenas y archiva temporal si queda vacío */
    public function retorno(array $data): void
    {
        DB::transaction(function () use ($data) {
            foreach ($data['colmenas'] as $colmenaId) {
                $col = Colmena::lockForUpdate()->findOrFail($colmenaId);
                MovimientoColmena::create([
                    'colmena_id'         => $col->id,
                    'apiario_origen_id'  => $data['apiario_temporal_id'],
                    'apiario_destino_id' => $data['apiario_destino_id'],
                    'fecha_inicio_mov'   => $data['fecha_retorno'],
                    'tipo_movimiento'    => 'retorno',
                ]);
                $col->update(['apiario_id' => $data['apiario_destino_id']]);
            }
            if (Colmena::where('apiario_id', $data['apiario_temporal_id'])->count() === 0) {
                Apiario::whereKey($data['apiario_temporal_id'])->update(['activo' => false]);
            }
        });
    }
}
