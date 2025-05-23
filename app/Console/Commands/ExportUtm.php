<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comuna;
use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

class ExportUtm extends Command
{
    /**
     * Nombre y firma del comando.
     *
     * @var string
     */
    protected $signature = 'utm:export
                            {--path= : Ruta donde guardar el JSON (por defecto storage/app/comunas-utm.json)}';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Exporta todas las comunas con coordenadas UTM a un JSON';

    /**
     * Ejecuta el comando.
     *
     * @return int
     */
    public function handle()
    {
        $outputPath = $this->option('path')
            ? base_path($this->option('path'))
            : storage_path('app/comunas-utm.json');

        $this->info("📍 Exportando UTM de comunas a: {$outputPath}");

        // Inicializa Proj4php
        $proj4   = new Proj4php();
        $projWGS = new Proj('EPSG:4326', $proj4);   // WGS84
        $projUTM = new Proj('EPSG:32719', $proj4);  // UTM zona 19S (Chile)

        $data = Comuna::all()->map(function (Comuna $comuna) use ($proj4, $projWGS, $projUTM) {
            $lon = $comuna->lon;
            $lat = $comuna->lat;

            $ptSrc  = new Point($lon, $lat, $projWGS);
            $ptDest = $proj4->transform($projUTM, $ptSrc);

            $huso = floor(($lon + 180) / 6) + 1;

            return [
                'id'       => $comuna->id,
                'utm_x'    => round($ptDest->x, 6),
                'utm_y'    => round($ptDest->y, 6),
                'utm_huso' => $huso,
            ];
        })->toArray();

        file_put_contents($outputPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("✅ Export completado. Total comunas: " . count($data));

        return 0;
    }
}
