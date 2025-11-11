<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductosArchivadosEliminados extends Mailable
{
    use Queueable, SerializesModels;

    public $productos;

    /**
     * Create a new message instance.
     */
    public function __construct($productos)
    {
        //
        $this->productos = $productos;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Productos Archivados Eliminados',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.productos_eliminados',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->subject('Productos eliminados por antigüedad')
            ->view('emails.productos_eliminados')
            ->with(['productos' => $this->productos]);
    }
}
