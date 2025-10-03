<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovimientoTrasladoRequest;
use App\Http\Requests\MovimientoRetornoRequest;
use App\Services\GeoService;
use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\MovimientoColmena;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    public function __construct(private GeoService $geo) {}

    public function traslado(MovimientoTrasladoRequest $r)
    {
        [$rid, $cid] = $this->geo->resolve($r->input('region'), $r->input('comuna'), $r->integer('region_id'), $r->integer('comuna_id'));

        return DB::transaction(function () use ($r, $rid, $cid) {
            $tmp = Apiario::create([
                'nombre'    => $r->string('nombre_temporal'),
                'tipo'      => 'temporal',
                'region_id' => $rid,
                'comuna_id' => $cid,
                'activo'    => true
            ]);

            foreach ($r->input('colmenas') as $colmenaId) {
                $colmena = Colmena::lockForUpdate()->findOrFail($colmenaId);
                MovimientoColmena::create([
                    'colmena_id'         => $colmena->id,
                    'apiario_origen_id'  => $r->integer('apiario_origen_id'),
                    'apiario_destino_id' => $tmp->id,
                    'fecha_inicio_mov'   => $r->date('fecha_inicio'),
                    'fecha_termino_mov'  => $r->date('fecha_termino'),
                    'motivo'             => $r->input('motivo'),
                    'tipo_movimiento'    => 'traslado',
                    'transportista'      => $r->input('transportista'),
                    'vehiculo'           => $r->input('vehiculo'),
                    'observaciones'      => $r->input('observaciones'),
                ]);
                $colmena->update(['apiario_id'=>$tmp->id]);
            }

            return response()->json(['ok'=>true,'apiario_temporal'=>$tmp->only(['id','nombre'])]);
        });
    }

    public function retorno(MovimientoRetornoRequest $r)
    {
        return DB::transaction(function () use ($r) {
            foreach ($r->input('colmenas') as $colmenaId) {
                $colmena = Colmena::lockForUpdate()->findOrFail($colmenaId);
                MovimientoColmena::create([
                    'colmena_id'         => $colmena->id,
                    'apiario_origen_id'  => $r->integer('apiario_temporal_id'),
                    'apiario_destino_id' => $r->integer('apiario_destino_id'),
                    'fecha_inicio_mov'   => $r->date('fecha_retorno'),
                    'tipo_movimiento'    => 'retorno',
                ]);
                $colmena->update(['apiario_id'=>$r->integer('apiario_destino_id')]);
            }

            // Archivar temporal si quedó vacío
            $quedan = Colmena::where('apiario_id', $r->integer('apiario_temporal_id'))->count();
            if ($quedan === 0) {
                Apiario::whereKey($r->integer('apiario_temporal_id'))->update(['activo'=>false]);
            }

            return ['ok'=>true];
        });
    }
}