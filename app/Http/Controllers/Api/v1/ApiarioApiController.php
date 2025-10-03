<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiarioRequest;
use App\Http\Requests\UpdateApiarioRequest;
use App\Models\Apiario;
use Illuminate\Http\Request;
use App\Http\Resources\ApiarioResource;
use App\Services\ApiarioService;
use App\Services\GeoService;
use Illuminate\Support\Facades\Cache;

class ApiarioApiController extends Controller
{
    public function __construct(
        private ApiarioService $svc,
        private GeoService $geo
    ) {}

    public function index()
    {
        $q = Apiario::query();

        // aceptar ?tipo=fijo o ?tipo_apiario=fijo
        $tipo = request('tipo') ?? request('tipo_apiario');
        if ($tipo) {
            $q->where('tipo_apiario', $tipo);
        }

        if (request()->filled('activo')) {
            $q->where('activo', (bool) request('activo'));
        }

        return ApiarioResource::collection($q->latest('id')->paginate(20));
    }

    public function store(StoreApiarioRequest $request)
    {
        $key = $request->header('Idempotency-Key');
        $cacheKey = $key
            ? 'idem:apiarios:' . $request->user()->id . ':' . $key
            : null;

        // 1) Responder el mismo recurso si ya se creó con esa key
        if ($cacheKey && ($id = Cache::get($cacheKey))) {
            $apiario = Apiario::findOrFail($id);
            return (new ApiarioResource($apiario))
                ->response()
                ->setStatusCode(201);
        }

        // 2) Crear (o llamar a tu servicio de creación)
        $apiario = Apiario::create([
            'user_id'      => $request->user()->id,
            'nombre'       => $request->input('nombre'),
            'tipo_apiario' => $request->input('tipo_apiario'),
            'region_id'    => $request->input('region_id'),
            'comuna_id'    => $request->input('comuna_id'),
            'activo'       => true,
            // ...otros campos que correspondan
        ]);

        // 3) Guardar el id para la misma Idempotency-Key
        if ($cacheKey) {
            Cache::put($cacheKey, $apiario->id, now()->addMinutes(10));
        }

        return (new ApiarioResource($apiario))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateApiarioRequest $r, Apiario $apiario)
    {
        [$rid, $cid] = $this->geo->resolve(
            $r->input('region'), $r->input('comuna'),
            $r->integer('region_id'), $r->integer('comuna_id')
        );

        $payload = array_merge($r->validated(), [
            'region_id'     => $rid   ?? $apiario->region_id,
            'comuna_id'     => $cid   ?? $apiario->comuna_id,
            'tipo_apiario'  => $r->input('tipo_apiario', $r->input('tipo', $apiario->tipo_apiario)),
        ]);

        $updated = $this->svc->actualizar($apiario, $payload);
        return new ApiarioResource($updated);
    }

    public function show(Apiario $apiario)
    {
        $apiario->loadCount('colmenas');
        return new ApiarioResource($apiario->loadCount('colmenas'));
    }

    public function destroy(Apiario $apiario)
    {
        $apiario->update(['activo'=>false]); 
        return response()->json(['ok'=>true]);
    }

    public function restore(Request $request, $id) {
        $apiario = Apiario::onlyTrashed()->where('user_id',$request->user()->id)->findOrFail($id);
        $this->authorize('restore', $apiario);
        $apiario->restore();

        $this->audit($request, 'apiario.restore', $apiario->id, $apiario->toArray());
        return response()->json(['ok'=>true, 'data'=>$apiario]);
    }

    private function audit(Request $r, string $action, int $resourceId, array $payload = []) {
        \DB::table('events')->insert([
            'user_id'     => $r->user()->id,
            'action'      => $action,
            'resource'    => 'apiario',
            'resource_id' => $resourceId,
            'payload'     => json_encode($payload),
            'ip'          => $r->ip(),
            'user_agent'  => (string)$r->userAgent(),
            'created_at'  => now(),
        ]);
    }

    
}

