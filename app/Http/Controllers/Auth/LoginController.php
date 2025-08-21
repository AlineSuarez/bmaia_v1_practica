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
            $defaultView = Auth::user()->preference->default_view ?? 'dashboard';
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
            // Si por alguna razón no existe en el mapa, vamos a dashboard
            $routeName = $map[$defaultView] ?? 'home';
            // redirect()->intended() usa la "intended URL" si venías de un middleware auth,
            // Redirigir a la página de inicio si las credenciales son correctas
            return redirect()->route($routeName); // valor calculado 
        }
        // Si falla, redirigir de nuevo al formulario con un mensaje de error
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