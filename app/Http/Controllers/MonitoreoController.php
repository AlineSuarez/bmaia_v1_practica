<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MonitoreoController extends Controller
{
    /**
     * Muestra el monitoreo histórico del clima.
     */
    public function index(Request $request)
    {
        // 1. ZONAS DE EJEMPLO (luego podemos cambiar a apiarios de la BD)
        $zonas = collect([
            (object)[
                'id'     => 'san_clemente',
                'nombre' => 'San Clemente',
                'lat'    => -35.5392,
                'lng'    => -71.4882,
            ],
            (object)[
                'id'     => 'santiago',
                'nombre' => 'Santiago',
                'lat'    => -33.4489,
                'lng'    => -70.6693,
            ],
            (object)[
                'id'     => 'temuco',
                'nombre' => 'Temuco',
                'lat'    => -38.7397,
                'lng'    => -72.5984,
            ],
        ]);

        // ID de zona seleccionada desde el selector (GET), o la primera por defecto
        $zonaId = $request->get('zona_id', $zonas->first()->id);
        $zonaSeleccionada = $zonas->firstWhere('id', $zonaId) ?? $zonas->first();

        // 2. RANGO DE FECHAS: último año (de hoy hacia atrás 1 año)
        $end   = Carbon::today();
        $start = $end->copy()->subYear(); // hace 1 año

        // 3. LLAMADA A LA API OPEN-METEO
        $response = Http::get('https://archive-api.open-meteo.com/v1/archive', [
            'latitude'   => $zonaSeleccionada->lat,
            'longitude'  => $zonaSeleccionada->lng,
            'start_date' => $start->toDateString(),
            'end_date'   => $end->toDateString(),
            'daily'      => 'temperature_2m_mean,temperature_2m_max,precipitation_sum,windspeed_10m_max',
            'timezone'   => 'auto',
        ]);

        // 4. PREPARAR ARRAYS PARA LA VISTA
        $labels    = [];
        $tempMean  = [];
        $tempMax   = [];
        $precip    = [];
        $windSpeed = [];

        if ($response->successful()) {
            $data = $response->json();

            // Nos aseguramos de que existan las claves antes de usarlas
            $labels    = $data['daily']['time']                 ?? [];
            $tempMean  = $data['daily']['temperature_2m_mean']  ?? [];
            $tempMax   = $data['daily']['temperature_2m_max']   ?? [];
            $precip    = $data['daily']['precipitation_sum']    ?? [];
            $windSpeed = $data['daily']['windspeed_10m_max']    ?? [];
        }

        // 5. RETORNAR LA VISTA CON TODOS LOS DATOS
        return view('hoja_ruta.monitoreo', compact(
            'zonas',
            'zonaSeleccionada',
            'labels',
            'tempMean',
            'tempMax',
            'precip',
            'windSpeed'
        ));
    }
}
