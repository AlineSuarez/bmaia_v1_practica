
<?php

// 1. Carga de autoload y arranque de Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 2. Importaciones
use App\Models\Comuna;
use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;


// 3. Inicialización de Proj4php
$proj4    = new Proj4php();
$projWGS  = new Proj('EPSG:4326', $proj4);   // WGS84
$projUTM  = new Proj('EPSG:32719', $proj4);  // UTM zona 19S (Chile)

// 4. Recorre todas las comunas y convierte
$output = [];

Comuna::all()->each(function(Comuna $comuna) use (&$output, $proj4, $projWGS, $projUTM) {
    // Coordenadas de entrada
    $lon = $comuna->lon;
    $lat = $comuna->lat;

    // Punto WGS84 → UTM
    $ptSrc  = new Point($lon, $lat, $projWGS);
    $ptDest = $proj4->transform($projUTM, $ptSrc);

    // Cálculo de huso UTM (1–60)
    $huso = floor(($lon + 180) / 6) + 1;

    $output[] = [
        'id'       => $comuna->id,
        'utm_x'    => round($ptDest->x, 6),
        'utm_y'    => round($ptDest->y, 6),
        'utm_huso' => $huso,
    ];
});

// 5. Guarda el JSON en storage/app/comunas-utm.json
$path = storage_path('app/comunas-utm.json');
file_put_contents($path, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✅ Export completado: {$path}\n";
