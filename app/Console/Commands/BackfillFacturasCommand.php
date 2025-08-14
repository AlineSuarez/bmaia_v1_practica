<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Factura;
use App\Models\User;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\Region;
use App\Models\Comuna;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class BackfillFacturasCommand extends Command
{
    protected $signature = 'facturas:backfill {--force : Regenera facturas aunque ya existan}';
    protected $description = 'Genera facturas y PDFs desde pagos aprobados que aún no tienen factura';

    public function handle()
    {
        $this->info('Iniciando backfill de facturas...');

        $porcIva = 19;

        $payments = Payment::where('status', 'paid')
            ->when(!$this->option('force'), function ($q) {
                $q->whereDoesntHave('factura');
            })
            ->with('user', 'datosFacturacion')
            ->get();

        if ($payments->isEmpty()) {
            $this->info('No hay pagos pendientes de facturar.');
            return SymfonyCommand::SUCCESS;
        }

        foreach ($payments as $payment) {
            $user = $payment->user;
            if (!$user) {
                $this->warn("Payment {$payment->id} sin usuario, saltando...");
                continue;
            }

            // Cálculo neto/iva a partir del total
            $total     = (int) $payment->amount;
            $neto      = (int) round($total / (1 + $porcIva / 100));
            $iva       = $total - $neto;
            $numero    = now()->format('Ymd') . '-' . strtoupper(Str::random(4));

            $snapshot  = $payment->billing_snapshot ?? [];
            $df        = $payment->datosFacturacion;

            if (empty($snapshot) && $df) {

                $regionNombre = null;
                    $comunaNombre = null;
                    try {
                        $regionNombre = method_exists($df, 'region') && $df->relationLoaded('region')
                            ? optional($df->region)->nombre
                            : (Region::find($df->region_id)->nombre ?? null);
                        $comunaNombre = method_exists($df, 'comuna') && $df->relationLoaded('comuna')
                            ? optional($df->comuna)->nombre
                            : (Comuna::find($df->comuna_id)->nombre ?? null);
                    } catch (\Throwable $e) {}
                $snapshot = [
                    'razon_social'           => $df->razon_social,
                    'rut'                    => $df->rut,
                    'giro'                   => $df->giro,
                    'direccion_comercial'    => $df->direccion_comercial,
                    'region_id'              => $df->region_id,
                    'comuna_id'              => $df->comuna_id,
                    'ciudad'                 => $df->ciudad,
                    'telefono'               => $df->telefono,
                    'correo'                 => $df->correo,
                    'autorizacion_envio_dte' => (bool) $df->autorizacion_envio_dte,
                    'correo_envio_dte'       => $df->correo_envio_dte,
                    'region_nombre'          => $regionNombre,
                    'comuna_nombre'          => $comunaNombre,
                ];
            }

            // Generar PDF usando tu vista oficial
            $pdf = Pdf::loadView('user.factura', [
                'user'       => $user,
                'payment'    => $payment,
                'montoNeto'  => $neto,
                'montoIva'   => $iva,
                'montoTotal' => $total,
                'numero'     => $numero,
                'snapshot'   => $snapshot,
            ]);

            $pdfFilename = 'facturas/' . $user->id . '/' . $numero . '.pdf';
            Storage::disk('public')->put($pdfFilename, $pdf->output());

            // Crear o actualizar factura
            Factura::updateOrCreate(
                ['payment_id' => $payment->id],
                [
                    'user_id'                    => $user->id,
                    'numero'                     => $numero,
                    'folio'                      => null,
                    'sii_track_id'               => null,
                    'estado'                     => 'emitida',
                    'monto_neto'                 => $neto,
                    'monto_iva'                  => $iva,
                    'monto_total'                => $total,
                    'porcentaje_iva'             => $porcIva,
                    'moneda'                     => 'CLP',
                    'fecha_emision'              => now(),
                    'fecha_vencimiento'          => now()->addDays(30),
                    'pdf_path'                   => $pdfFilename,
                    'xml_path'                   => null,
                    'pdf_url'                    => null,
                    'xml_url'                    => null,
                    'datos_facturacion_snapshot' => $snapshot,
                    'plan'                       => $payment->plan,
                ]
            );

            $this->info("Factura generada para payment {$payment->id} ({$numero})");
        }

        $this->info('Backfill finalizado.');
        return SymfonyCommand::SUCCESS;
    }
}
