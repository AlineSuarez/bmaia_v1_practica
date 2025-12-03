<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Exception;

class GoogleController extends Controller
{
    // Redirige a Google para la autenticación
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Maneja la respuesta de Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName() ?: 'Usuario',
                    'password' => bcrypt(Str::random(32)),
                ]
            );

            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->intended('/home');
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'No se pudo iniciar sesión con Google.');
        }
    }
}
