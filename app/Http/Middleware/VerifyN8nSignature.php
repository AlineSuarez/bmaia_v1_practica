<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyN8nSignature
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-Api-Key'); // o HMAC similar al cliente
        if ($key !== config('services.n8n.callback_key')) {
            return response()->json(['ok'=>false,'code'=>401,'message'=>'Unauthorized'], 401);
        }
        return $next($request);
    }
}
