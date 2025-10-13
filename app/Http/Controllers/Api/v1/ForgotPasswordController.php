<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    // Paso 1: Enviar email con link (y token)
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => ['required','email']]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)], 200);
        }

        return response()->json([
            'message' => __($status),
        ], 422);
    }

    // Paso 2: Consumir token y establecer nueva contraseña
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required','string'],
            'email'    => ['required','email'],
            'password' => ['required','string','min:6','confirmed'], // requiere password_confirmation
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 200);
        }

        return response()->json(['message' => __($status)], 422);
    }
}
