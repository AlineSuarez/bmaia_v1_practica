<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Idempotency
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('Idempotency-Key');
        if (!$key) {
            // Puedes exigirlo o generarlo. Recomiendo exigirlo en móviles.
            return response()->json(['ok'=>false,'code'=>400,'message'=>'Idempotency-Key requerido'], 400);
        }

        // 1) ¿Existe?
        $row = DB::table('integrations_requests')->where('idem_key', $key)->first();
        if ($row) {
            // Devolver respuesta cacheada
            return response($row->response_json, 200, ['Content-Type' => 'application/json']);
        }

        // 2) Continuar y capturar respuesta
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // 3) Guardar sólo si 2xx/201/204
        if ($response->isSuccessful()) {
            DB::table('integrations_requests')->updateOrInsert(
                ['idem_key' => $key],
                [
                    'response_json' => $response->getContent(),
                    'created_at'    => now(),
                ]
            );
        }

        return $response;
    }
}
