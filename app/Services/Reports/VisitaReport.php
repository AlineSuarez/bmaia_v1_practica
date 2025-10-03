<?php

namespace App\Services\Reports;

use App\Models\Visita;
use App\Services\Reports\Contracts\ReportGenerator;
use Barryvdh\DomPDF\Facade\Pdf;

class VisitaReport implements ReportGenerator
{
    public function generate(int|string $id): string
    {
        $visita = Visita::with([
                'apiario:id,nombre,lat,lon,user_id',
                'colmenas:id,apiario_id,numero,color',
                // agrega más relaciones si necesitas
            ])
            ->findOrFail($id);

        $pdf = PDF::loadView('pdf.visita-detalle', [
            'visita' => $visita,
        ]);

        return $pdf->output();
    }
}
