<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $datos;

    /**
     * Create a new message instance.
     */
    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $asunto = 'Contacto web B-MaiA: ' . $this->datos['nombre'] . ' [' . now()->format('d-m-Y H:i:s') . ']';

        return $this->from('contacto@bmaia.cl', 'B-MaiA | Contacto')
            ->subject($asunto)
            ->view('emails.contact-base')
            ->with(['datos' => $this->datos]);
    }
}