<?php
namespace App\Mail\Payments;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentVoidedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function build()
    {
        return $this->subject('ℹ️ Transacción anulada – B-MaiA')
            ->markdown('emails.payments.voided', ['p' => $this->payment]);
    }
}