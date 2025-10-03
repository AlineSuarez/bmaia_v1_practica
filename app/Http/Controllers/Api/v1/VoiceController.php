<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\N8nClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoiceController extends Controller
{
    public function recognize(Request $request, N8nClient $n8n)
    {
        // 1) Validación (multipart: audio + meta JSON opcional)
        $v = Validator::make($request->all(), [
            'audio' => 'required|file|mimetypes:audio/mpeg,audio/mp4,audio/x-m4a,audio/aac,audio/webm,video/webm',
            'meta'  => 'nullable|string',
        ]);
        if ($v->fails()) {
            return response()->json(['ok'=>false, 'code'=>422, 'errors'=>$v->errors()], 422);
        }

        $meta = [
            'usuario_id'   => $request->user()->id,
            'apiario_id'   => $request->input('apiario_id'),
            'region'       => $request->input('region'),
            'comuna'       => $request->input('comuna'),
            'colmenas'     => $request->input('colmenas', []),
            'observaciones'=> $request->input('observaciones'),
            'rango_fechas' => $request->input('rango_fechas'),
            'idioma'       => $request->input('idioma', 'es-CL'),
        ];

        // Si venía meta como string JSON, mezclar
        if ($request->filled('meta')) {
            $meta = array_merge($meta, json_decode($request->string('meta'), true) ?: []);
        }

        // 2) Idempotency-Key → propágalo a n8n
        $headers = [];
        if ($request->hasHeader('Idempotency-Key')) {
            $headers['Idempotency-Key'] = $request->header('Idempotency-Key');
        }

        // 3) Llamar a n8n (webhook que hicimos: /voice-ingreso)
        $filePath = $request->file('audio')->getRealPath();
        $res = $n8n->postMultipart('voice-ingreso', 'audio', $filePath, $meta, $headers);

        if (!$res->successful()) {
            return response()->json([
                'ok'      => false,
                'code'    => $res->status(),
                'message' => 'n8n error',
                'detail'  => $res->json() ?: $res->body(),
            ], $res->status() ?: 502);
        }

        // 4) Respuesta uniforme al móvil
        return response()->json($res->json(), $res->status());
    }

    public function respond(Request $request)
    {
        // Si quieres que n8n te notifique algo (callback), puedes procesarlo acá.
        return response()->json(['ok'=>true]);
    }
}
