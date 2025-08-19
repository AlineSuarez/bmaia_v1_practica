<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;

class FacturaController extends Controller
{
    public function show(Factura $factura)
    {
        // Seguridad: que solo el dueño (o admin) vea la factura
        if ($factura->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        // Normalizar snapshot
        $snap = $factura->datos_facturacion_snapshot;
        if (is_string($snap)) {
            $snap = json_decode($snap, true) ?? [];
        }

        // Calcular vencePlan igual que en el listado
        $duration = config('plans.duration_days', 365);
        $vencePlan = null;
        if ($factura->payment) {
            $vencePlan = $factura->payment->expires_at
                ?? optional($factura->payment->created_at)->copy()->addDays($duration);
        }
        if (!$vencePlan) {
            $vencePlan = optional($factura->fecha_emision)->copy()->addDays($duration);
        }

        // URLs de recursos (accessors del modelo)
        $pdfUrl = $factura->pdf_url;
        $xmlUrl = $factura->xml_url;

        return view('facturas.show', compact('factura', 'snap', 'vencePlan', 'pdfUrl', 'xmlUrl'));
    }

    public function descargar(Factura $factura)
    {
        // Permisos: solo propietario o admin
        if ($factura->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        // Si el modelo expone una URL pública (S3 u otro)
        if (!empty($factura->pdf_url)) {
            return redirect()->away($factura->pdf_url);
        }

        // Si el PDF está guardado en storage y hay un campo con la ruta
        $path = $factura->pdf_path ?? null;
        if ($path && \Storage::exists($path)) {
            return \Storage::download($path, ($factura->numero_mostrar ?? 'factura') . '.pdf');
        }

        // Alternativa: generar PDF dinámicamente (implementar si aplica)

        abort(404, 'PDF no disponible');
    }
}