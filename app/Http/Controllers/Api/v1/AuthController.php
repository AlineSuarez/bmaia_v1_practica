<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Google\Client as GoogleClient;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'device'   => ['nullable', 'string', 'max:60'], // p.ej. "flutter-app"
        ]);

        // No uses Auth::attempt() para APIs token-based (depende de sesión 'web')
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales inválidas.'],
            ]);
        }

        // Opcional: invalidar tokens antiguos del mismo device
        $deviceName = $data['device'] ?? 'mobile';
        $user->tokens()->where('name', $deviceName)->delete();

        // Habilidades/Scopes para limitar uso del token desde móvil
        $abilities = ['mobile', 'voice:write', 'read'];
        $token = $user->createToken($deviceName, $abilities)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ]
        ], 200);
    }

    public function me(Request $request)
    {
        $u = $request->user();
        return response()->json([
            'id'    => $u->id,
            'name'  => $u->name,
            'email' => $u->email,
        ], 200);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
            'device'                => ['nullable', 'string', 'max:60'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken($data['device'] ?? 'mobile', ['mobile', 'read'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ]
        ], 201);
    }

    public function logout(Request $request)
    {
        // Revoca solo el token actual (Bearer)
        optional($request->user()->currentAccessToken())->delete();

        return response()->json(['message' => 'Sesión cerrada'], 200);
    }

    // ===== GOOGLE LOGIN PARA MÓVIL =====
    public function loginWithGoogle(Request $request)
    {
        $data = $request->validate([
            'id_token' => ['required','string'],
            'device'   => ['nullable','string','max:60'],
        ]);

        $client = new GoogleClient(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($request->id_token);

        if (!$payload) {
            return response()->json(['message' => 'Token de Google inválido'], 401);
        }

        $email = $payload['email'] ?? null;
        $name  = $payload['name']  ?? 'Usuario';
        if (!$email) {
            return response()->json(['message' => 'Email no disponible en Google'], 422);
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => bcrypt(Str::random(40))]
        );

        $device = $data['device'] ?? 'mobile';
        $user->tokens()->where('name', $device)->delete();
        $token = $user->createToken($device, ['mobile','read'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => ['id'=>$user->id,'name'=>$user->name,'email'=>$user->email],
        ]);
    }
}
