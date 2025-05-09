<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $mensaje = 'Prueba de conexión controlador-vista';
        // Agrega aquí el resto de variables que usas en la vista
        return view('home', [
            'mensaje' => $mensaje,
        ]);
    }

}
