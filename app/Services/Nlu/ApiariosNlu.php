<?php

namespace App\Services\Nlu;

use Illuminate\Support\Str;
use App\Models\Region;
use App\Models\Comuna;

class ApiariosNlu
{
    public function parse(string $text): array
    {
        $t = Str::of($text)->lower()->toString();

        // Intent
        $intent = match (true) {
            preg_match('/\b(crea(r)?|nuevo|registrar)\b.*\bapiario\b/u', $t)        => 'apiario.crear',
            preg_match('/\b(editar|cambia(r)?|actualiza(r)?)\b.*\bapiario\b/u', $t) => 'apiario.editar',
            preg_match('/\b(elimina(r)?|borrar|archivar)\b.*\bapiario\b/u', $t)     => 'apiario.eliminar',
            preg_match('/\b(lista(r)?|mostrar|ver)\b.*\bapiarios?\b/u', $t)         => 'apiario.listar',
            preg_match('/\b(detalle|estado|ver)\b.*\bapiario\b/u', $t)              => 'apiario.detalle',
            preg_match('/\b(traslada(r)?|mover)\b.*\bcolmenas?\b/u', $t)            => 'apiario.movimiento.crearTemporal',
            preg_match('/\b(retorna(r)?|volver)\b.*\bcolmenas?\b/u', $t)            => 'apiario.movimiento.retorno',
            preg_match('/\b(pdf|ficha|documento)\b.*\bapiario\b/u', $t)             => 'apiario.documento.pdf',
            default => 'meta.unknown',
        };

        // Slots básicos
        $slots = [
            'nombre' => $this->extractNombre($t),
            'tipo'   => $this->extractTipo($t),
            'colmenas_iniciales' => $this->extractNumero($t),
        ];

        // Región/Comuna por catálogo (búsqueda suave)
        [$region, $comuna] = $this->extractRegionComuna($t);
        if ($region) $slots['region'] = $region;
        if ($comuna) $slots['comuna'] = $comuna;

        // Fechas para movimientos
        if (preg_match('/(\d{4}-\d{2}-\d{2})\s*(al|hasta)\s*(\d{4}-\d{2}-\d{2})/u', $t, $m)) {
            $slots['fecha_inicio'] = $m[1];
            $slots['fecha_termino']= $m[3];
        }

        // “desde X a Y” (nombre temporal)
        if (preg_match('/temporal\s+(?<tmp>[\pL0-9\s\-]+)/u', $t, $m)) {
            $slots['nombre_temporal'] = trim($m['tmp']);
        }

        return ['intent'=>$intent, 'slots'=>array_filter($slots, fn($v)=>$v!==null && $v!=='')];
    }

    private function extractNombre(string $t): ?string
    {
        if (preg_match('/apiario\s+(?<nom>[\pL0-9\-\s]{2,})\s+(en|de|con|tipo|,|\.)/u', $t, $m)) {
            return trim($m['nom']);
        }
        if (preg_match('/llamado\s+(?<nom>[\pL0-9\-\s]+)/u', $t, $m)) {
            return trim($m['nom']);
        }
        return null;
    }

    private function extractTipo(string $t): ?string
    {
        if (str_contains($t,'fijo')) return 'fijo';
        if (str_contains($t,'trashumante')) return 'trashumante';
        if (str_contains($t,'temporal')) return 'temporal';
        return null;
    }

    private function extractNumero(string $t): ?int
    {
        if (preg_match('/(\d{1,4})\s*(colmenas?)/u', $t, $m)) return intval($m[1]);
        if (preg_match('/con\s+(\d{1,4})/u', $t, $m)) return intval($m[1]);
        return null;
    }

    private function extractRegionComuna(string $t): array
    {
        $catalogR = Region::pluck('nombre')->all();
        $catalogC = Comuna::pluck('nombre')->all();

        $norm = fn($s)=> Str::of($s)
            ->lower()->replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'])
            ->replaceMatches('/[^a-z0-9]+/','')->toString();

        $tn = $norm($t);

        $foundR = null;
        foreach ($catalogR as $r) {
            if (Str::contains($tn, $norm($r))) { $foundR = $r; break; }
        }

        $foundC = null;
        foreach ($catalogC as $c) {
            if (Str::contains($tn, $norm($c))) { $foundC = $c; break; }
        }

        return [$foundR, $foundC];
    }
}
