<?php
namespace App\Services;

use App\Mail\Payments\PaymentFailedMail;
use App\Mail\Payments\PaymentSucceededMail;
use App\Mail\Payments\PaymentVoidedMail;
use App\Mail\Payments\ReceiptMail;
use App\Mail\Plans\PlanActivatedMail;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class PaymentMailer
{
    /** Normaliza y deduplica una lista (string|array) de emails */
    protected static function normalizeEmails(string|array|null $emails): array
    {
        if (!$emails) return [];
        if (is_string($emails)) {
            // soporta "a@x.com, b@x.com"
            $emails = preg_split('/[,;]+/', $emails);
        }
        $clean = [];
        foreach ($emails as $e) {
            $e = trim(strtolower($e));
            if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)) {
                $clean[$e] = true; // usa keys para dedupe
            }
        }
        return array_keys($clean);
    }

    protected static function toBilling(User $user)
    {
        try {
            $toList = self::normalizeEmails($user->email);
            $ccList = self::normalizeEmails($user->datosFacturacion?->correo_envio_dte);

            if ($ccList) {
                $toSet = array_flip($toList);
                $ccList = array_values(array_filter($ccList, fn($cc) => !isset($toSet[strtolower($cc)])));
            }

            $m = \Mail::to($toList);
            if (!empty($ccList)) {
                $m->cc($ccList);
            }
            return $m;
        } catch (\Throwable $e) {
            \Log::warning('[MAIL] Envío deshabilitado o mal configurado: '.$e->getMessage());
            // Stub que ignora queue()/send() para no romper flujo
            return new class {
                public function queue($x = null){ return $this; }
                public function send($x = null){ return $this; }
                public function cc($x = null){ return $this; }
            };
        }
    }

    public static function sendSucceeded(Payment $p): void
    {
        self::toBilling($p->user)->queue(new PaymentSucceededMail($p));
    }

    public static function sendFailed(Payment $p): void
    {
        self::toBilling($p->user)->queue(new PaymentFailedMail($p));
    }

    public static function sendVoided(Payment $p): void
    {
        self::toBilling($p->user)->queue(new PaymentVoidedMail($p));
    }

    public static function sendPlanActivated(User $user, string $plan): void
    {
        self::toBilling($user)->queue(new PlanActivatedMail($user, $plan));
    }

    public static function sendReceipt(Payment $payment): void
    {
        try {
            $user = $payment->user;
            $pdfUrl = null;

            if ($payment->receipt_pdf_path && \Storage::disk('public')->exists($payment->receipt_pdf_path)) {
                $pdfUrl = \Storage::disk('public')->url($payment->receipt_pdf_path);
            }

            \Mail::to($user->email)
                ->queue(new ReceiptMail($payment, $pdfUrl));

        } catch (\Throwable $e) {
            \Log::error('Error enviando comprobante: '.$e->getMessage(), ['payment_id' => $payment->id]);
        }
    }

}