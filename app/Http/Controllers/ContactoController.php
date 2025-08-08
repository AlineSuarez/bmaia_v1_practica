<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactoMail;

class ContactoController extends Controller
{
    public function enviar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:120',
            'email' => 'required|email|max:160',
            'telefono' => 'nullable|string|max:20',
            'empresa' => 'nullable|string|max:160',
            'tipo' => 'required|string',
            'asunto' => 'required|string|max:150',
            'mensaje' => 'required|string|min:10|max:2000',
            'acepta_politica' => 'accepted',
            'website' => 'max:0',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'tipo.required' => 'El tipo de consulta es obligatorio.',
            'asunto.required' => 'El asunto es obligatorio.',
            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.min' => 'El mensaje debe tener al menos :min caracteres.',
            'mensaje.max' => 'El mensaje no puede superar los :max caracteres.',
            'acepta_politica.accepted' => 'Debes aceptar la Política de Privacidad.',
            'website.max' => 'Spam detectado.',
        ]);

        // Enviar correo
        Mail::to('agente.bmaia@gmail.com')->send(new ContactoMail($request->all()));

        return redirect()->route('contacto.form')->with('success', '¡Mensaje enviado correctamente!');
    }
}