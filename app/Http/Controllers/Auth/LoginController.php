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
        return view('auth.login'); // Asegúrate de que esta vista existe
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
            $defaultView = Auth::user()->preference->default_view ?? 'home';
            // Mapa de keys → route names (falta analizar que los nombres coincidan)
            $map = [
                'dashboard' => 'dashboard',
                'apiaries' => 'apiarios',
                'calendar' => 'tareas.calendario',
                'reports' => 'dashboard',
                'home' => 'home',
                'cuaderno' => 'visitas.index',
                'tareas' => 'tareas',
                'zonificacion' => 'zonificacion',
                'sistemaexperto' => 'sistemaexperto',
            ];
            $routeName = $map[$defaultView] ?? 'home';
            return redirect()->route($routeName);
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
        return redirect('/'); // Redirige al inicio
    }
}