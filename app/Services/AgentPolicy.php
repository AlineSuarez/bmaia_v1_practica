<?php

namespace App\Services;

class AgentPolicy
{
    /** Reglas de slots mínimos por intent */
    private array $rules = [
        'apiario.crear' => ['nombre','tipo', ['region','region_id'], ['comuna','comuna_id']],
        'apiario.movimiento.crearTemporal' => ['apiario_origen_id','nombre_temporal', ['region','region_id'], ['comuna','comuna_id'],'fecha_inicio','fecha_termino','colmenas'],
        'apiario.movimiento.retorno' => ['apiario_temporal_id','apiario_destino_id','colmenas','fecha_retorno'],
    ];

    /**
     * Devuelve arreglo de campos faltantes. Los grupos (arrays) significan “al menos uno”.
     */
    public function missingSlots(string $intent, array $slots): array
    {
        $req = $this->rules[$intent] ?? [];
        $missing = [];
        foreach ($req as $item) {
            if (is_array($item)) {
                $ok = false;
                foreach ($item as $alt) {
                    if (array_key_exists($alt, $slots) && !is_null($slots[$alt])) { $ok = true; break; }
                }
                if (!$ok) $missing[] = implode(' / ', $item);
            } else {
                if (!array_key_exists($item, $slots) || is_null($slots[$item])) $missing[] = $item;
            }
        }
        return $missing;
    }
}
