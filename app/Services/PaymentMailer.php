<?php
namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use SendGrid;
use SendGrid\Mail\Mail as SendGridMail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class PaymentMailer
{
    /** Normaliza y deduplica una lista (string|array) de emails */
    protected static function normalizeEmails(string|array|null $emails): array
    {
        if (!$emails)
            return [];
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

    /** Envío genérico usando SendGrid */
    protected static function sendWithSendGrid(array $toList, array $ccList, string $subject, string $plainText, string $htmlContent, array $attachments = []): void
    {
        $email = new SendGridMail();
        $email->setFrom("soporte@bmaia.cl", "B-MaiA");
        foreach ($toList as $to) {
            $email->addTo($to);
        }
        foreach ($ccList as $cc) {
            $email->addCc($cc);
        }
        $email->setSubject($subject);
        $email->addContent("text/plain", $plainText);
        $email->addContent("text/html", $htmlContent);

        // Adjuntos (solo para sendReceipt)
        foreach ($attachments as $attachment) {
            $email->addAttachment(
                $attachment['content'],
                $attachment['type'],
                $attachment['name'],
                'attachment'
            );
        }

        $sendgrid = new SendGrid(config('services.sendgrid.api_key'));
        try {
            $response = $sendgrid->send($email);
            \Log::info('SendGrid response', [
                'status' => $response->statusCode(),
                'body' => $response->body(),
                'headers' => $response->headers(),
                'to' => $toList,
                'cc' => $ccList,
                'subject' => $subject,
            ]);
            
            if ($response->statusCode() >= 400) {
                throw new \Exception('SendGrid error: ' . $response->body());
            }
        } catch (\Throwable $e) {
            \Log::error('SendGrid error: ' . $e->getMessage(), [
                'to' => $toList,
                'cc' => $ccList,
                'subject' => $subject,
            ]);
        }
    }

    protected static function getToAndCc(User $user): array
    {
        $toList = self::normalizeEmails($user->email);
        $ccList = self::normalizeEmails($user->datosFacturacion?->correo_envio_dte);

        if ($ccList) {
            $toSet = array_flip($toList);
            $ccList = array_values(array_filter($ccList, fn($cc) => !isset($toSet[strtolower($cc)])));
        }
        return [$toList, $ccList];
    }

    public static function sendSucceeded(Payment $p): void
    {
        $user = $p->user;
        [$toList, $ccList] = self::getToAndCc($user);
        $subject = "Pago realizado con éxito en B-MaiA";
        $plainText = "¡Tu pago fue realizado con éxito!";
        $htmlContent = View::make('emails.payments.succeeded', ['payment' => $p, 'user' => $user])->render();

        self::sendWithSendGrid($toList, $ccList, $subject, $plainText, $htmlContent);
    }

    public static function sendFailed(Payment $p): void
    {
        $user = $p->user;
        [$toList, $ccList] = self::getToAndCc($user);
        $subject = "Pago fallido en B-MaiA";
        $plainText = "Tu pago no pudo ser procesado. Por favor, intenta nuevamente.";
        $htmlContent = View::make('emails.payments.failed', ['p' => $p])->render();

        self::sendWithSendGrid($toList, $ccList, $subject, $plainText, $htmlContent);
    }

    public static function sendVoided(Payment $p): void
    {
        $user = $p->user;
        [$toList, $ccList] = self::getToAndCc($user);
        $subject = "Transacción anulada en B-MaiA";
        $plainText = "La transacción para el plan {$p->plan} fue anulada.";
        $htmlContent = View::make('emails.payments.voided', ['p' => $p])->render();

        self::sendWithSendGrid($toList, $ccList, $subject, $plainText, $htmlContent);
    }

    public static function sendPlanActivated(User $user, string $plan): void
    {
        [$toList, $ccList] = self::getToAndCc($user);
        $subject = "Plan activado en B-MaiA";
        $plainText = "¡Tu plan {$plan} ha sido activado!";
        $htmlContent = View::make('emails.plans.activated', ['user' => $user, 'plan' => $plan])->render();

        self::sendWithSendGrid($toList, $ccList, $subject, $plainText, $htmlContent);
    }

    public static function sendReceipt(Payment $payment): void
    {
        $user = $payment->user;
        [$toList, $ccList] = self::getToAndCc($user);
        $subject = "Comprobante de pago B-MaiA";
        $plainText = "Adjuntamos el comprobante de tu pago.";
        $pdfUrl = null;
        $attachments = [];

        if ($payment->receipt_pdf_path && Storage::disk('public')->exists($payment->receipt_pdf_path)) {
            $pdfPath = Storage::disk('public')->path($payment->receipt_pdf_path);
            $attachments[] = [
                'content' => base64_encode(file_get_contents($pdfPath)),
                'type' => 'application/pdf',
                'name' => 'comprobante.pdf',
            ];
        }

        // Agrega aquí los datos de tu empresa
        $empresa = [
            'razon' => 'B-MaiA SpA',
            'rut' => '76.123.456-7',
        ];

        $htmlContent = View::make('payment.receipt', [
            'payment' => $payment,
            'user' => $user,
            'empresa' => $empresa,
            'pdfUrl' => $pdfUrl,
        ])->render();

        self::sendWithSendGrid($toList, $ccList, $subject, $plainText, $htmlContent, $attachments);
    }
}