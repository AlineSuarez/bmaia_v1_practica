<?php

namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiarioRequest;
use App\Http\Requests\UpdateApiarioRequest;
use App\Services\GeoService;
use App\Models\Apiario;
use App\Models\Colmena;
use Illuminate\Support\Facades\DB;

class RecordController extends Controller
{
    public function __construct(private GeoService $geo) {}

    public function index()
    {
        $q = Apiario::query();
        if ($t = request('tipo')) $q->where('tipo',$t);
        if (!is_null(request('activo'))) $q->where('activo', (bool) request('activo'));
        return $q->latest('id')->paginate(20);
    }

    public function store(StoreApiarioRequest $r)
    {
        [$rid, $cid] = $this->geo->resolve(
            $r->input('region'), $r->input('comuna'),
            $r->integer('region_id'), $r->integer('comuna_id')
        );

        $apiario = DB::transaction(function () use ($r, $rid, $cid) {
            $a = Apiario::create([
                'nombre'    => $r->string('nombre'),
                'tipo'      => $r->string('tipo'),
                'region_id' => $rid,
                'comuna_id' => $cid,
                'latitud'   => $r->input('latitud'),
                'longitud'  => $r->input('longitud'),
                'direccion' => $r->input('direccion'),
                'activo'    => true
            ]);

            if ($n = $r->integer('colmenas_iniciales')) {
                Colmena::factory()->count($n)->create(['apiario_id' => $a->id]);
            }
            return $a;
        });

        return response()->json(['ok'=>true,'apiario'=>$apiario->fresh('colmenas')], 201);
    }

    public function show(Apiario $apiario)
    {
        return $apiario->loadCount('colmenas');
    }

    public function update(UpdateApiarioRequest $r, Apiario $apiario)
    {
        [$rid, $cid] = $this->geo->resolve(
            $r->input('region'), $r->input('comuna'),
            $r->integer('region_id'), $r->integer('comuna_id')
        );

        $apiario->fill($r->safe()->except(['region','comuna','region_id','comuna_id']));
        if ($rid) $apiario->region_id = $rid;
        if ($cid) $apiario->comuna_id = $cid;
        $apiario->save();

        return ['ok'=>true,'apiario'=>$apiario];
    }

    public function destroy(Apiario $apiario)
    {
        $apiario->update(['activo'=>false]);
        // opcional: soft delete si tu modelo lo usa
        return response()->json(['ok'=>true]);
    }
}