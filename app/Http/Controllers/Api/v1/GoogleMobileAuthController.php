<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;

class GoogleMobileAuthController extends Controller
{
    public function signIn(Request $request)
    {
        $data = $request->validate([
            'id_token' => 'required|string',
        ]);

        // Verificamos el id_token contra Google
        $resp = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $data['id_token'],
        ]);

        if (!$resp->ok()) {
            return response()->json(['message' => 'Invalid Google token'], 401);
        }

        $payload = $resp->json();

        // Validaciones de seguridad mínimas
        $aud = $payload['aud'] ?? null;         // debe ser tu Web Client ID
        $iss = $payload['iss'] ?? null;         // accounts.google.com / https://accounts.google.com
        $email = $payload['email'] ?? null;
        $emailVerified = filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $expectedAud = config('services.google.client_id_mobile'); // lo configuramos más abajo

        if (!$aud || $aud !== $expectedAud) {
            return response()->json(['message' => 'Token audience mismatch'], 401);
        }
        if (!in_array($iss, ['accounts.google.com', 'https://accounts.google.com'], true)) {
            return response()->json(['message' => 'Invalid token issuer'], 401);
        }
        if (!$email || !$emailVerified) {
            return response()->json(['message' => 'Email not verified'], 422);
        }

        // Crear/obtener usuario
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'               => $payload['name'] ?? Str::before($email, '@'),
                'password'           => bcrypt(Str::random(40)), // placeholder (no se usa)
                'email_verified_at'  => now(),
                'google_id'          => $payload['sub'] ?? null, // guarda el "sub" de Google
            ]
        );

        // Actualiza avatar / nombre si viene
        $user->forceFill([
            'name'   => $payload['name'] ?? $user->name,
            'avatar' => $payload['picture'] ?? $user->avatar,
            'google_id' => $user->google_id ?: ($payload['sub'] ?? null),
        ])->save();

        // Token de API (Sanctum)
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'avatar'=> $user->avatar,
            ],
        ]);
    }
}
