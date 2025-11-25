<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RouteTimeController extends Controller
{
    public function calc(Request $request)
    {
        $request->validate([
            'from' => 'required|string|min:3',
            'to'   => 'required|string|min:3',
        ]);

        $from = $request->query('from');
        $to   = $request->query('to');

        // Nominatim pide un User-Agent identificable. Usa tu correo.
        $ua = 'bmaia/1.0 (' . (env('NOMINATIM_EMAIL', 'contact@example.com')) . ')';
        $headers = ['User-Agent' => $ua];

        // 1) Geocodificar origen
        $geoFrom = Http::withHeaders($headers)
            ->timeout(12)
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $from,
                'format' => 'jsonv2',
                'limit' => 1,
            ])->json();

        if (!$geoFrom || empty($geoFrom[0]['lat']) || empty($geoFrom[0]['lon'])) {
            return response()->json(['ok' => false, 'error' => 'No se pudo geocodificar el origen.'], 422);
        }

        // 2) Geocodificar destino
        $geoTo = Http::withHeaders($headers)
            ->timeout(12)
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $to,
                'format' => 'jsonv2',
                'limit' => 1,
            ])->json();

        if (!$geoTo || empty($geoTo[0]['lat']) || empty($geoTo[0]['lon'])) {
            return response()->json(['ok' => false, 'error' => 'No se pudo geocodificar el destino.'], 422);
        }

        $oLat = $geoFrom[0]['lat']; $oLon = $geoFrom[0]['lon'];
        $dLat = $geoTo[0]['lat'];   $dLon = $geoTo[0]['lon'];

        // 3) Ruta OSRM (auto). Devuelve distance (m) y duration (s)
        $osrm = Http::timeout(12)->get(
            "https://router.project-osrm.org/route/v1/driving/{$oLon},{$oLat};{$dLon},{$dLat}",
            [
                'overview'     => 'false',
                'alternatives' => 'false',
                'steps'        => 'false',
            ]
        )->json();

        if (!$osrm || ($osrm['code'] ?? '') !== 'Ok' || empty($osrm['routes'][0])) {
            return response()->json(['ok' => false, 'error' => 'No se pudo calcular la ruta.'], 422);
        }

        $route    = $osrm['routes'][0];
        $distance = (float)($route['distance'] ?? 0.0); // metros
        $duration = (float)($route['duration'] ?? 0.0); // segundos

        return response()->json([
            'ok'          => true,
            'from'        => ['name' => $geoFrom[0]['display_name'] ?? $from, 'lat' => $oLat, 'lon' => $oLon],
            'to'          => ['name' => $geoTo[0]['display_name'] ?? $to,   'lat' => $dLat, 'lon' => $dLon],
            'distance_m'  => $distance,
            'duration_s'  => $duration,
        ]);
    }
}
