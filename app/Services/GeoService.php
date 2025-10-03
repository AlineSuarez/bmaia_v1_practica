<?php

namespace App\Services;

use App\Models\Region;
use App\Models\Comuna;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class GeoService
{
    /**
     * Recibe (region, comuna) por nombre o IDs y devuelve [region_id, comuna_id].
     * Tolerante a typos menores (normaliza, acentos, mayúsculas).
     */
    public function resolve(?string $regionName, ?string $comunaName, ?int $regionId = null, ?int $comunaId = null): array
    {
        // Prioridad: IDs válidos
        if ($regionId && Region::find($regionId)) {
            $rid = $regionId;
        }

        if ($comunaId && Comuna::find($comunaId)) {
            $cid = $comunaId;
        }

        // Cache catálogo normalizado por 12h
        [$regions, $comunas] = Cache::remember('geo.catalogs.v1', 43200, function () {
            return [
                Region::select('id','nombre')->get()->map(fn($r)=>[
                    'id'=>$r->id, 'key'=>$this->norm($r->nombre)
                ]),
                Comuna::select('id','nombre','region_id')->get()->map(fn($c)=>[
                    'id'=>$c->id, 'key'=>$this->norm($c->nombre), 'rid'=>$c->region_id
                ]),
            ];
        });

        // Si no hay IDs, intentamos por nombres
        if (!isset($rid) && $regionName) {
            $key = $this->norm($regionName);
            $match = collect($regions)->first(fn($r)=>Str::startsWith($r['key'],$key) || $r['key']===$key);
            if ($match) $rid = $match['id'];
        }

        if (!isset($cid) && $comunaName) {
            $key = $this->norm($comunaName);
            $match = collect($comunas)->first(fn($c)=>Str::startsWith($c['key'],$key) || $c['key']===$key);
            if ($match) {
                $cid = $match['id'];
                // Si no hay región, infiere por comuna
                $rid = $rid ?? $match['rid'];
            }
        }

        return [ $rid ?? null, $cid ?? null ];
    }

    private function norm(string $s): string
    {
        $s = Str::lower($s);
        $s = str_replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'], $s);
        return preg_replace('/[^a-z0-9]+/','',$s);
    }
}
