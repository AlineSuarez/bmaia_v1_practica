<?php
namespace App\Http\Controllers;

use Auth;

class SettingsController extends Controller
{
    // Método para mostrar la configuración de la cuenta
    public function settings()
    {
        return view('user.settings');
    }
}