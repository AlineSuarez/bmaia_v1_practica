<?php

// app/Http/Controllers/Api/AssistantApiController.php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Nlu\ApiariosNlu;
use App\Services\AgentPolicy;
use App\Services\ApiarioService;
use App\Services\GeoService;
use App\Models\Apiario;
use Illuminate\Support\Arr;

class AssistantApiController extends Controller
{
    public function respond(
        Request $r,
        ApiariosNlu $nlu,
        AgentPolicy $policy,
        ApiarioService $svc,
        GeoService $geo
    ) {
        $text = (string) $r->input('text','');

        $parsed = $nlu->parse($text);
        $intent = $parsed['intent'];
        $slots  = $parsed['slots'];

        $missing = $policy->missingSlots($intent, $slots);
        if ($missing) {
            return response()->json([
                'ok'=>false,
                'action'=>'ask_missing',
                'missing'=>$missing,
                'message'=>"Me falta: ".implode(', ', $missing)."."
            ], 422);
        }

        // Ejecutar
        try {
            return match ($intent) {
                'apiario.crear' => $this->crearApiario($svc, $geo, $slots),
                'apiario.movimiento.crearTemporal' => $this->traslado($svc, $geo, $slots),
                'apiario.movimiento.retorno'       => $this->retorno($svc, $slots),
                'apiario.listar'  => ['ok'=>true,'items'=>Apiario::latest('id')->limit(20)->get()],
                'apiario.detalle' => ['ok'=>true,'hint'=>'Usa /api/v1/apiarios/{id} en la app'],
                'apiario.documento.pdf' => ['ok'=>true,'hint'=>'POST /api/v1/apiarios/{id}/pdf'],
                default => ['ok'=>false,'message'=>'No entendí la solicitud. Prueba: "Crea apiario Las Palmas en Colbún, Maule con 10 colmenas, tipo fijo".']
            };
        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'error'=>$e->getMessage()], 500);
        }
    }

    private function crearApiario(ApiarioService $svc, GeoService $geo, array $s)
    {
        [$rid,$cid] = $geo->resolve($s['region'] ?? null, $s['comuna'] ?? null, null, null);
        $apiario = $svc->crear([
            'nombre' => $s['nombre'],
            'tipo'   => $s['tipo'],
            'region_id'=>$rid, 'comuna_id'=>$cid,
            'colmenas_iniciales'=> Arr::get($s,'colmenas_iniciales')
        ]);
        return ['ok'=>true,'message'=>"Apiario **{$apiario->nombre}** creado (#{$apiario->id}).",'apiario'=>$apiario];
    }

    private function traslado(ApiarioService $svc, GeoService $geo, array $s)
    {
        [$rid,$cid] = $geo->resolve($s['region'] ?? null, $s['comuna'] ?? null, null, null);
        $tmp = $svc->traslado([
            'apiario_origen_id'=>$s['apiario_origen_id'],
            'nombre_temporal'=>$s['nombre_temporal'],
            'region_id'=>$rid, 'comuna_id'=>$cid,
            'fecha_inicio'=>$s['fecha_inicio'],
            'fecha_termino'=>$s['fecha_termino'] ?? null,
            'colmenas'=>$s['colmenas'] ?? [],
            'motivo'=>$s['motivo'] ?? null,
        ]);
        return ['ok'=>true,'message'=>"Temporal **{$tmp->nombre}** creado (#{$tmp->id}).",'apiario_temporal'=>$tmp];
    }

    private function retorno(ApiarioService $svc, array $s)
    {
        $svc->retorno($s);
        return ['ok'=>true,'message'=>"Retorno realizado."];
    }
}
