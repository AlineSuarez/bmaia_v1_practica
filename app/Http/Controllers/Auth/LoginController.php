<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validar las credenciales
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Verificar si el usuario no tiene plan o está vencido
            if (!$user->plan || ($user->fecha_vencimiento && now()->greaterThan($user->fecha_vencimiento))) {
                // Verificar si ya tuvo plan gratuito
                $hadFreeTrial = \App\Models\Payment::where('user_id', $user->id)
                    ->where('plan', 'drone')
                    ->exists();

                if (!$hadFreeTrial) {
                    // Asignar plan gratuito automáticamente
                    $user->plan = 'drone';
                    $user->fecha_vencimiento = now()->addDays(16);
                    $user->save();

                    // Crear registro de pago gratuito
                    \App\Models\Payment::create([
                        'user_id' => $user->id,
                        'transaction_id' => 'trial-login-' . uniqid(),
                        'status' => 'paid',
                        'amount' => 0,
                        'plan' => 'drone',
                        'doc_type' => 'boleta',
                        'expires_at' => now()->addDays(16),
                    ]);
                }
            }

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas son incorrectas.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}