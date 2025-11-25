<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RouteTimeController extends Controller
{
    public function calc(Request $request)
    {
        // 1) Validar parámetros
        $request->validate([
            'from' => 'required|string|min:3',
            'to'   => 'required|string|min:3',
        ]);

        // Texto tal como lo escribió el usuario
        $fromRaw = trim($request->query('from'));
        $toRaw   = trim($request->query('to'));

        // Por si acaso, si no incluye "Chile", lo agregamos.
        // Igual usamos countrycodes=cl, pero esto ayuda a Nominatim.
        $fromQ = $fromRaw;
        $toQ   = $toRaw;

        if (stripos($fromQ, 'chile') === false) {
            $fromQ .= ', Chile';
        }
        if (stripos($toQ, 'chile') === false) {
            $toQ .= ', Chile';
        }

        // Configuración Nominatim
        $ua      = 'bmaia/1.0 (' . (env('NOMINATIM_EMAIL', 'contact@example.com')) . ')';
        $headers = ['User-Agent' => $ua];
        $country = env('NOMINATIM_COUNTRYCODES', 'cl');
        $timeout = (int) env('ROUTE_HTTP_TIMEOUT', 12);

        // 2) Geocodificar ORIGEN
        $geoFrom = Http::withHeaders($headers)
            ->timeout($timeout)
            ->get('https://nominatim.openstreetmap.org/search', [
                'q'            => $fromQ,
                'format'       => 'jsonv2',
                'limit'        => 1,
                'countrycodes' => $country,
                'addressdetails' => 1,
            ])->json();

        if (!$geoFrom || empty($geoFrom[0]['lat']) || empty($geoFrom[0]['lon'])) {
            return response()->json([
                'ok'    => false,
                'error' => 'No se pudo geocodificar el origen. Revisa la dirección (ej: "Tucán 981, Maipú").',
            ], 422);
        }

        // 3) Geocodificar DESTINO
        $geoTo = Http::withHeaders($headers)
            ->timeout($timeout)
            ->get('https://nominatim.openstreetmap.org/search', [
                'q'            => $toQ,
                'format'       => 'jsonv2',
                'limit'        => 1,
                'countrycodes' => $country,
                'addressdetails' => 1,
            ])->json();

        if (!$geoTo || empty($geoTo[0]['lat']) || empty($geoTo[0]['lon'])) {
            return response()->json([
                'ok'    => false,
                'error' => 'No se pudo geocodificar el destino. Revisa la dirección.',
            ], 422);
        }

        $oLat = (float)$geoFrom[0]['lat'];
        $oLon = (float)$geoFrom[0]['lon'];
        $dLat = (float)$geoTo[0]['lat'];
        $dLon = (float)$geoTo[0]['lon'];

        // 4) Llamar a OSRM para ruta
        $osrmBase = rtrim(env('OSRM_BASE_URL', 'https://router.project-osrm.org'), '/');
        $profile  = env('OSRM_PROFILE', 'driving');
        $osrmUrl  = "{$osrmBase}/route/v1/{$profile}/{$oLon},{$oLat};{$dLon},{$dLat}";

        $wantGeom = $request->boolean('geom'); // incluir geometría para el mapa
        $params = [
            'overview'     => $wantGeom ? 'full' : 'false',
            'alternatives' => 'false',
            'steps'        => 'false',
        ];
        if ($wantGeom) {
            $params['geometries'] = 'geojson';
        }

        $osrmResponse = Http::timeout($timeout)->get($osrmUrl, $params);

        if (!$osrmResponse->ok()) {
            return response()->json([
                'ok'    => false,
                'error' => 'Error al consultar el servicio de rutas (OSRM).',
            ], 500);
        }

        $osrm = $osrmResponse->json();

        if (!$osrm || ($osrm['code'] ?? '') !== 'Ok' || empty($osrm['routes'][0])) {
            return response()->json([
                'ok'    => false,
                'error' => 'No se pudo calcular la ruta.',
            ], 422);
        }

        $route    = $osrm['routes'][0];
        $distance = (float)($route['distance'] ?? 0.0); // metros
        $duration = (float)($route['duration'] ?? 0.0); // segundos

        $payload = [
            'ok'          => true,

            // Te devuelvo tanto lo que escribió el usuario como lo que entendió Nominatim
            'from_input'  => $fromRaw,
            'to_input'    => $toRaw,

            'from'        => [
                'name' => $geoFrom[0]['display_name'] ?? $fromRaw,
                'lat'  => $oLat,
                'lon'  => $oLon,
            ],
            'to'          => [
                'name' => $geoTo[0]['display_name'] ?? $toRaw,
                'lat'  => $dLat,
                'lon'  => $dLon,
            ],
            'distance_m'  => $distance,
            'duration_s'  => $duration,
        ];

        if ($wantGeom && !empty($route['geometry'])) {
            // GeoJSON LineString con coords [lon,lat]
            $payload['geometry'] = $route['geometry'];
        }

        return response()->json($payload);
    }
}
