<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatoFacturacion;
use App\Models\Factura;
use Illuminate\Support\Facades\Mail;
use App\Mail\FacturaGeneradaMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class DatoFacturacionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'rut' => 'required|string|max:255',
            'giro' => 'nullable|string|max:255',
            'direccion_comercial' => 'nullable|string|max:255',
            'billing_region' => 'nullable|integer|exists:regiones,id',
            'billing_comuna' => 'nullable|integer|exists:comunas,id',
            'ciudad' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255',
            'autorizacion_envio_dte' => 'nullable|boolean',
            'correo_envio_dte' => 'nullable|email|max:255',
        ]);

        $userId = $request->user()->id;

        $datos = DatoFacturacion::firstOrNew(['user_id' => $userId]);
        $datos->fill([
            'razon_social' => $request->razon_social,
            'rut' => $request->rut,
            'giro' => $request->giro,
            'direccion_comercial' => $request->direccion_comercial,
            'region_id' => $request->billing_region,
            'comuna_id' => $request->billing_comuna,
            'ciudad' => $request->ciudad,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'autorizacion_envio_dte' => $request->boolean('autorizacion_envio_dte'),
            'correo_envio_dte' => $request->correo_envio_dte,
        ]);

        $datos->user_id = $userId; // importante si es nuevo
        $datos->save();

        return back()->with('success', 'Datos de facturación guardados correctamente.');
    }

    public function enviarCorreo(Request $request, Factura $factura)
    {
        $correoDestino = $request->input('correo') ?? $factura->user->email;

        try {
            if (!filter_var($correoDestino, FILTER_VALIDATE_EMAIL)) {
                return back()->with('error', 'Correo no válido.');
            }

            // Adjuntar PDF si está en disco público
            $mailable = new FacturaGeneradaMail($factura);

            if (!empty($factura->pdf_path) && \Storage::disk('public')->exists($factura->pdf_path)) {
                $fileContents = \Storage::disk('public')->get($factura->pdf_path);
                $filename = basename($factura->pdf_path);
                $mailable->attachData($fileContents, $filename, [
                    'mime' => 'application/pdf'
                ]);
            }

            Mail::to($correoDestino)->send($mailable);

            return back()->with('success', 'Factura enviada correctamente al correo.');
        } catch (\Throwable $e) {
            \Log::error('Error al enviar factura por correo: ' . $e->getMessage());
            return back()->with('error', 'No se pudo enviar la factura.');
        }
    }

    public function download(Factura $factura)
    {
        abort_unless($factura->user_id === Auth::id(), 403);

        $path = $factura->pdf_path;           // ej: facturas/123/20240814-ABCD.pdf
        $disk = 'public';                     // o 'local' si lo guardaste ahí

        abort_unless(Storage::disk($disk)->exists($path), 404);

        $filename = 'Factura-' . $factura->numero . '.pdf';
        return response()->download(Storage::disk($disk)->path($path), $filename);
    }

    public function view(Factura $factura)
    {
        abort_unless($factura->user_id === Auth::id(), 403);

        $path = $factura->pdf_path;
        $disk = 'public'; // o 'local'
        abort_unless(Storage::disk($disk)->exists($path), 404);

        return response()->file(Storage::disk($disk)->path($path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $factura->numero . '.pdf"',
        ]);
    }

}
