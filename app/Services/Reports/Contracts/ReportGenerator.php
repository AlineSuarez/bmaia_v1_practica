<?php
namespace App\Services\Reports\Contracts;

interface ReportGenerator {
    /** Retorna binario PDF */
    public function generate(int|string $id): string;
}