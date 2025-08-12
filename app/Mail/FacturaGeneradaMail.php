<?php

namespace App\Mail;

use App\Models\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FacturaGeneradaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * La factura a enviar.
     *
     * @var \App\Models\Factura
     */
    public $factura;

    /**
     * Crear una nueva instancia del mensaje.
     *
     * @param \App\Models\Factura $factura
     */
    public function __construct(Factura $factura)
    {
        $this->factura = $factura;
    }

    /**
     * Construir el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        // Ruta segura del PDF
        $pdfPath = storage_path('app/' . $this->factura->pdf_path);

        return $this->subject('Tu Factura #' . $this->factura->numero_mostrar)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.factura') // Blade del contenido del correo
            ->with([
                'factura' => $this->factura,
                'usuario' => $this->factura->user,
            ])
            ->attach($pdfPath, [
                'as' => 'Factura-' . $this->factura->numero_mostrar . '.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
