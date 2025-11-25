<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GeoProxyController extends Controller
{
    /**
     * Proxy para WMS/WFS/WCS/GetFeatureInfo/tiles.
     * Uso: /wms-proxy?url=https://host/geoserver/wms&service=WMS&request=GetMap...
     */
    public function proxy(Request $request)
    {
        $base = $request->query('url');
        if (!$base) {
            return response()->json(['error' => 'Missing url'], 400);
        }

        // --- Lista blanca de hosts permitidos (agrega/elimina según uses)
        $allowedHosts = [
            'ide.chile.gob.cl',
            'ide.mma.gob.cl',
            'mapas.conaf.cl',
            'arclim.mma.gob.cl',
            'www.ide.cl',
        ];
        $host = parse_url($base, PHP_URL_HOST);
        if (!$host || !in_array($host, $allowedHosts, true)) {
            return response()->json(['error' => 'Host not allowed: '.$host], 403);
        }

        // --- Construye los parámetros a reenviar (todo menos "url")
        $forwardParams = $request->query();
        unset($forwardParams['url']);

        try {
            // En algunos servidores hay problemas de TLS o SNI -> verify=false ayuda.
            // También agrego cabeceras típicas "browser-like".
            $client = Http::withOptions([
                        'verify'  => false,       // <— si tu PHP no tiene CA bundle actualizado
                        'timeout' => 30,
                    ])->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (B-MaiA GeoProxy)',
                        'Accept'     => '*/*',
                        'Referer'    => url('/'),
                    ]);

            // En lugar de concatenar, dejo que Http::get arme la query.
            $resp = $client->get($base, $forwardParams);

            $status = $resp->status();
            $ctype  = $resp->header('Content-Type', 'application/octet-stream');
            $body   = $resp->body();

            // Si el servidor remoto devolvió error, propágalo tal cual para depurar
            if ($status >= 400) {
                // log (para ver en storage/logs/laravel.log)
                Log::warning('GeoProxy upstream error', [
                    'target' => $base,
                    'status' => $status,
                    'ctype'  => $ctype,
                    'len'    => strlen($body),
                ]);
            }

            return response($body, $status)->withHeaders([
                'Content-Type'                => $ctype,
                'Access-Control-Allow-Origin' => '*',
                'Cache-Control'               => 'public, max-age=300',
            ]);

        } catch (\Throwable $e) {
            Log::error('GeoProxy exception', [
                'target' => $base,
                'msg'    => $e->getMessage(),
            ]);
            return response()->json([
                'error'   => 'Proxy request failed',
                'details' => $e->getMessage(),
            ], 502);
        }
    }

    /**
     * Ping simple para probar conectividad con el WMS (GetCapabilities).
     * Ej: /wms-proxy/ping?url=https://ide.mma.gob.cl/geoserver/wms
     */
    public function ping(Request $request)
    {
        $base = $request->query('url');
        if (!$base) {
            return response()->json(['error' => 'Missing url'], 400);
        }
        $params = [
            'service' => 'WMS',
            'request' => 'GetCapabilities',
        ];
        return $this->proxy(new Request(array_merge($request->all(), ['url'=>$base] + $params)));
    }
}
