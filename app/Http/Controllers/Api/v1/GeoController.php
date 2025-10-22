<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GeoService;

class GeoController extends Controller
{
    /**
     * GET /api/v1/geo/resolve?region=Valparaiso&comuna=La%20Ligua
     * (requiere auth:sanctum según tus grupos de rutas)
     */
    public function resolve(Request $request, GeoService $geo)
    {
        $regionName = $request->query('region');
        $comunaName = $request->query('comuna');
        $regionId   = $request->query('region_id') ? (int) $request->query('region_id') : null;
        $comunaId   = $request->query('comuna_id') ? (int) $request->query('comuna_id') : null;

        [$rid, $cid] = $geo->resolve($regionName, $comunaName, $regionId, $comunaId);

        return response()->json([
            'ok'        => (bool) ($rid || $cid),
            'region_id' => $rid,
            'comuna_id' => $cid,
        ]);
    }
}
