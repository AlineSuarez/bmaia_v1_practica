<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Region;
use App\Models\Comuna;
use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

class ObtenerCoordenadasComunas extends Command
{
    /**
     * El nombre y la firma del comando.
     *
     * @var string
     */
    protected $signature = 'coordenadas:obtener';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Obtiene las coordenadas de las comunas faltantes a través de la API de Nominatim y actualiza la base de datos';

    /**
     * Ejecuta el comando.
     *
     * @return int
     */
    public function handle()
    {
        // Lista de las comunas faltantes
        $faltantes = [
            'Región de Valparaíso' => [
                'Juan Fernández', 'Puchuncaví', 'Quintero', 'Limache', 'Olmué', 'Isla de Pascua'
            ],
            'Región Metropolitana de Santiago' => [
                'Pirque', 'San José de Maipo', 'Quinta Normal'
            ],
            'Región de O’Higgins' => [
                'Olivar', 'Paredones', 'Quinta de Tilcoco', 'Chépica'
            ],
            'Región del Maule' => [
                'Empedrado', 'Rauco', 'San Javier'
            ],
            'Región de Ñuble' => [
                'Coelemu'
            ],
            'Región del Biobío' => [
                'Alto Biobío'
            ],
            'Región de La Araucanía' => [
                'Curacautín', 'Lonquimay'
            ],
            'Región de Los Lagos' => [
                'Cochamó', 'Puerto Octay', 'Puyehue', 'Chaitén', 'Hualaihué', 'Futaleufú', 'Palena'
            ],
        ];

        foreach ($faltantes as $regionNombre => $comunas) {
            $region = Region::where('nombre', $regionNombre)->first();

            if (!$region) {
                $this->warn("⚠️ Región no encontrada: $regionNombre (debes crearla primero)");
                continue;
            }

            foreach ($comunas as $comunaNombre) {
                // Obtenemos las coordenadas de la API
                $coordinates = $this->getCoordinatesFromAPI($comunaNombre);

                if ($coordinates) {
                    $lat = $coordinates['lat'];
                    $lon = $coordinates['lon'];

                    // Convertir lat/lon a UTM
                    $utm = $this->convertToUTM($lat, $lon);

                    // Actualizamos la comuna con las coordenadas y valores UTM
                    Comuna::updateOrCreate(
                        ['nombre' => $comunaNombre, 'region_id' => $region->id], // Condición para buscar
                        [
                            'lat' => $lat,
                            'lon' => $lon,
                            'utm_x' => $utm['utm_x'],
                            'utm_y' => $utm['utm_y'],
                            'utm_huso' => $utm['utm_huso'],
                            'updated_at' => now(), // Actualizamos la fecha de modificación
                        ]
                    );

                    $this->info("✅ Comuna actualizada: $comunaNombre ({$region->nombre})");
                } else {
                    $this->warn("⚠️ No se pudo obtener coordenadas para: $comunaNombre");
                }

                // Pausa de 2 segundos entre cada solicitud para evitar límite de consultas
                sleep(2);
            }
        }

        return 0;
    }

    private function getCoordinatesFromAPI($comunaNombre)
    {
        // API de Nominatim (OpenStreetMap)
        $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($comunaNombre . ", Chile") . "&format=json&addressdetails=1&limit=1";

        // Realizamos la solicitud a la API de Nominatim
        $response = Http::withHeaders([
            'User-Agent' => 'B-MaiA/1.0' 
        ])->get($url);

        if ($response->successful()) {
            $data = $response->json();

            // Verificamos si obtuvimos resultados
            if (isset($data[0])) {
                // Si la respuesta tiene datos válidos
                return [
                    'lat' => $data[0]['lat'],
                    'lon' => $data[0]['lon'],
                ];
            } else {
                return null;
            }
        } else {
            $this->warn("❌ Error al obtener datos de la API para: $comunaNombre");
            return null;
        }
    }

    private function convertToUTM($lat, $lon)
    {
        // Inicializamos Proj4php
        $proj4 = new Proj4php();
        $projWGS = new Proj('EPSG:4326', $proj4);   // WGS84
        $projUTM = new Proj('EPSG:32719', $proj4);  // UTM zona 19S (Chile)

        // Creamos el punto de coordenadas WGS84
        $ptSrc = new Point($lon, $lat, $projWGS);

        // Transformamos las coordenadas a UTM
        $ptDest = $proj4->transform($projUTM, $ptSrc);

        // Devolvemos las coordenadas UTM
        return [
            'utm_x' => round($ptDest->x, 6),
            'utm_y' => round($ptDest->y, 6),
            'utm_huso' => 19,  // Zona UTM 19S para Chile
        ];
    }
}
