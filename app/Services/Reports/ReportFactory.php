<?php
namespace App\Services\Reports;

use App\Services\Reports\Contracts\ReportGenerator;
use InvalidArgumentException;

class ReportFactory
{
    /** @var array<string, class-string<ReportGenerator>> */
    protected array $map = [
        'apiario' => ApiarioReport::class,
        'visita'  => VisitaReport::class,

        // en el futuro:
        // 'alimentacion' => AlimentacionReport::class,
        // 'reina'        => ReinaReport::class,
        // 'medicamentos' => MedicamentosReport::class,
        // 'inspeccion'   => InspeccionReport::class,
    ];

    public function make(string $type): ReportGenerator
    {
        $key = strtolower($type);
        if (!isset($this->map[$key])) {
            throw new InvalidArgumentException("Reporte no soportado: {$type}");
        }
        return app($this->map[$key]); // resuelto por el contenedor de Laravel
    }
}