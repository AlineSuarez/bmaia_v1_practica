<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * PATCH /api/v1/me
     * Actualiza datos básicos del perfil (name, email, phone, etc.)
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            // Ajusta/añade los campos que realmente existan en tu tabla users
            'phone' => ['sometimes', 'nullable', 'string', 'max:60'],
        ]);

        $user->fill($data);
        $user->save();

        return response()->json($this->serializeUser($user), 200);
    }

    /**
     * POST /api/v1/me/avatar
     * Sube o reemplaza el avatar del usuario
     * Campo: avatar (archivo)
     */
    public function uploadAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB
        ]);

        // Borra avatar anterior si existe
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        // Guarda nuevo
        $ext      = $request->file('avatar')->getClientOriginalExtension();
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $ext;
        $path     = $request->file('avatar')->storeAs('avatars/'.$user->id, $filename, 'public');

        // Asume que tienes una columna `avatar_path` en users
        $user->avatar_path = $path;
        $user->save();

        return response()->json($this->serializeUser($user), 200);
    }

    /**
     * PATCH /api/v1/me/password
     * Cambia la contraseña del usuario
     * Campos: current_password, password, password_confirmation
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password'      => ['required', 'string'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'message' => 'La contraseña actual no es correcta.',
            ], 422);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'message' => 'Contraseña actualizada correctamente.',
        ], 200);
    }

    private function serializeUser($user): array
    {
        $avatarUrl = null;
        if ($user->profile_picture) {
            $publicPath = Storage::url($user->profile_picture);
            $avatarUrl  = request()->getSchemeAndHttpHost() . $publicPath;
        }

        return [
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'phone'  => $user->phone ?? null,
            'avatar' => $avatarUrl,
            'profile_picture' => $user->profile_picture,
            'avatar_url'      => $avatarUrl, 
        ];
    }
}
