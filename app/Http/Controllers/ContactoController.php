<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SendGrid;
use Exception;
use Illuminate\Support\Facades\View;

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

        // Datos del formulario
        $data = $request->all();

        // Renderiza la vista Blade como HTML
        $htmlContent = View::make('emails.contact-base', ['datos' => $data])->render();

        // Construir el asunto único
        $uniqueSubject = $data['asunto'] . ' [' . now()->format('Y-m-d H:i:s') . ']';

        // Construir el correo
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("contacto@bmaia.cl", "B-MaiA - Contacto");
        $email->setSubject($uniqueSubject);
        $email->addTo("agente.bmaia@gmail.com", "Agente B-MaiA");
        $email->addContent(
            "text/plain",
            "Nombre: {$data['nombre']}\nEmail: {$data['email']}\nTeléfono: {$data['telefono']}\nEmpresa: {$data['empresa']}\nTipo: {$data['tipo']}\nMensaje: {$data['mensaje']}"
        );
        $email->addContent(
            "text/html",
            $htmlContent
        );

        // Envía el correo usando la API de SendGrid
        $sendgrid = new SendGrid(config('services.sendgrid.api_key'));
        try {
            $response = $sendgrid->send($email);
            \Log::info('SendGrid response', [
                'status' => $response->statusCode(),
                'body' => $response->body(),
                'headers' => $response->headers(),
            ]);
        } catch (Exception $e) {
            \Log::error('SendGrid error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'No se pudo enviar el correo. Intenta más tarde.']);
        }

        return redirect()->route('contacto.form')->with('success', '¡Mensaje enviado correctamente!');
    }
}