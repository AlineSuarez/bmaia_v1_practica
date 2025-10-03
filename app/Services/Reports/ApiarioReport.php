<?php
namespace App\Services\Reports;

use App\Models\Apiario;
use App\Services\Reports\Contracts\ReportGenerator;
use Barryvdh\DomPDF\Facade\Pdf;

class ApiarioReport implements ReportGenerator {
    public function generate(int|string $id): string {
        $apiario = Apiario::withCount('colmenas')
            ->with(['colmenas' => fn($q) => $q->orderBy('id')->limit(100)])
            ->findOrFail($id);

        $pdf = Pdf::loadView('pdf.apiario-resumen', ['apiario' => $apiario]);
        return $pdf->output(); // binario
    }
}