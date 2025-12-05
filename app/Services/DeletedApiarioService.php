<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DeletedApiarioService
{
    const EXPIRATION_DAYS = 16;
    const CACHE_PREFIX = 'deleted_apiario_';

    /**
     * Guardar apiario eliminado en Redis por 16 días
     */
    public function storeDeletedApiario($apiario, $userId)
    {
        $key = self::CACHE_PREFIX . $apiario->id;

        // Construir ubicación desde región y comuna si existe
        $ubicacion = $apiario->ubicacion;
        if (!$ubicacion && ($apiario->region || $apiario->comuna)) {
            $parts = [];
            if ($apiario->comuna && $apiario->comuna->nombre) {
                $parts[] = $apiario->comuna->nombre;
            }
            if ($apiario->region && $apiario->region->nombre) {
                $parts[] = $apiario->region->nombre;
            }
            $ubicacion = !empty($parts) ? implode(', ', $parts) : null;
        }

        $data = [
            'id' => $apiario->id,
            'user_id' => $userId,
            'nombre' => $apiario->nombre,
            'ubicacion' => $ubicacion,
            'latitud' => $apiario->latitud,
            'longitud' => $apiario->longitud,
            'colmenas_count' => $apiario->colmenas()->count(),
            'deleted_at' => Carbon::now()->toIso8601String(),
            'expires_at' => Carbon::now()->addDays(self::EXPIRATION_DAYS)->toIso8601String(),
            // Guardar todos los datos del apiario
            'apiario_data' => $apiario->toArray(),
            // Guardar colmenas relacionadas
            'colmenas' => $apiario->colmenas()->get()->toArray(),
        ];

        // Guardar en Redis por 16 días (16 * 24 * 60 = 23040 minutos)
        Cache::put($key, $data, now()->addDays(self::EXPIRATION_DAYS));

        // Agregar a la lista de apiarios eliminados
        $this->addToDeletedList($apiario->id);

        return $data;
    }

    /**
     * Agregar ID a la lista de apiarios eliminados
     */
    private function addToDeletedList($apiarioId)
    {
        $listKey = 'deleted_apiarios_list';
        $list = Cache::get($listKey, []);

        if (!in_array($apiarioId, $list)) {
            $list[] = $apiarioId;
            Cache::put($listKey, $list, now()->addDays(self::EXPIRATION_DAYS + 1));
        }
    }

    /**
     * Remover ID de la lista de apiarios eliminados
     */
    private function removeFromDeletedList($apiarioId)
    {
        $listKey = 'deleted_apiarios_list';
        $list = Cache::get($listKey, []);

        $list = array_diff($list, [$apiarioId]);
        Cache::put($listKey, $list, now()->addDays(self::EXPIRATION_DAYS + 1));
    }

    /**
     * Obtener todos los apiarios eliminados
     */
    public function getAllDeleted()
    {
        $listKey = 'deleted_apiarios_list';
        $ids = Cache::get($listKey, []);

        $deletedApiarios = [];
        foreach ($ids as $id) {
            $data = $this->getDeletedApiario($id);
            if ($data) {
                // Calcular tiempo restante
                $expiresAt = Carbon::parse($data['expires_at']);
                $now = Carbon::now();

                $data['remaining_days'] = $now->diffInDays($expiresAt, false);
                $data['remaining_hours'] = $now->diffInHours($expiresAt, false) % 24;
                $data['remaining_minutes'] = $now->diffInMinutes($expiresAt, false) % 60;
                $data['remaining_seconds'] = $now->diffInSeconds($expiresAt, false) % 60;
                $data['is_expired'] = $expiresAt->isPast();

                $deletedApiarios[] = $data;
            }
        }

        // Ordenar por fecha de eliminación (más recientes primero)
        usort($deletedApiarios, function($a, $b) {
            return strtotime($b['deleted_at']) - strtotime($a['deleted_at']);
        });

        return $deletedApiarios;
    }

    /**
     * Obtener un apiario eliminado específico
     */
    public function getDeletedApiario($apiarioId)
    {
        $key = self::CACHE_PREFIX . $apiarioId;
        return Cache::get($key);
    }

    /**
     * Restaurar apiario desde Redis
     */
    public function restoreApiario($apiarioId)
    {
        $data = $this->getDeletedApiario($apiarioId);

        if (!$data) {
            return null;
        }

        // Preparar datos del apiario desde apiario_data
        $apiarioData = $data['apiario_data'];

        // Remover campos que no deben insertarse (timestamps automáticos, id)
        unset($apiarioData['id']);
        unset($apiarioData['created_at']);
        unset($apiarioData['updated_at']);
        unset($apiarioData['deleted_at']);
        unset($apiarioData['colmenas']); // Las colmenas se restauran por separado

        // Recrear el apiario en la base de datos con todos sus campos
        $apiario = \App\Models\Apiario::create($apiarioData);

        // Restaurar colmenas si existen
        if (!empty($data['colmenas'])) {
            foreach ($data['colmenas'] as $colmenaData) {
                // Remover campos que no deben insertarse
                unset($colmenaData['id']);
                unset($colmenaData['created_at']);
                unset($colmenaData['updated_at']);
                unset($colmenaData['deleted_at']);

                // Asignar el nuevo ID del apiario
                $colmenaData['apiario_id'] = $apiario->id;

                \App\Models\Colmena::create($colmenaData);
            }
        }

        // Eliminar de Redis
        $this->permanentlyDelete($apiarioId);

        return $apiario;
    }

    /**
     * Eliminar permanentemente de Redis
     */
    public function permanentlyDelete($apiarioId)
    {
        $key = self::CACHE_PREFIX . $apiarioId;
        Cache::forget($key);
        $this->removeFromDeletedList($apiarioId);
    }

    /**
     * Contar apiarios eliminados
     */
    public function count()
    {
        $listKey = 'deleted_apiarios_list';
        $ids = Cache::get($listKey, []);
        return count($ids);
    }

    /**
     * Limpiar apiarios expirados (ejecutar con cron)
     */
    public function cleanExpired()
    {
        $deleted = $this->getAllDeleted();
        $cleaned = 0;

        foreach ($deleted as $data) {
            if ($data['is_expired']) {
                $this->permanentlyDelete($data['id']);
                $cleaned++;
            }
        }

        return $cleaned;
    }
}
