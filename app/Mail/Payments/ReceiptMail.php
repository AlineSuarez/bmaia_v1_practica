<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment, public ?string $pdfUrl = null) {}

    public function build()
    {
        $m = $this->subject('Comprobante de pago');

        // Si prefieres adjuntar el PDF en vez de link:
        if ($this->payment->receipt_pdf_path && \Storage::disk('public')->exists($this->payment->receipt_pdf_path)) {
            $m->attachFromStorageDisk('public', $this->payment->receipt_pdf_path, 'comprobante.pdf');
        }

        return $m->markdown('emails.payment.receipt', [
            'payment' => $this->payment,
            'pdfUrl'  => $this->pdfUrl,
        ]);
    }
}
