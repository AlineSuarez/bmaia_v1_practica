<?php

namespace App\Http\Controllers;

use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\MovimientoColmena;
use App\Models\SistemaExperto;
use App\Models\EstadoNutricional;
use App\Models\PresenciaVarroa;
use App\Models\PresenciaNosemosis;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ColmenaController extends Controller
{
    public function index(Apiario $apiario)
    {
        // Solo mostrar títulos si es trashumante temporal
        $mostrarTitulos = ($apiario->tipo_apiario === 'trashumante' && $apiario->es_temporal);

        if ($mostrarTitulos) {
            $movs = MovimientoColmena::with('apiarioOrigen', 'colmena')
                ->where('apiario_destino_id', $apiario->id)
                ->where('tipo_movimiento', 'traslado')
                ->get();

            $colmenasPorApiarioBase = $movs
                ->groupBy(fn($m) => optional($m->apiarioOrigen)->nombre ?: 'Sin apiario base')
                ->map(fn($grupo) => $grupo->pluck('colmena'));

            $apiariosBaseSeleccionados = $colmenasPorApiarioBase->keys()->all();
        } else {
            // caso no-temporal: muestro todas las colmenas bajo un único título
            $col = $apiario->colmenas()->get();
            $colmenasPorApiarioBase = collect([$apiario->nombre => $col]);
            $apiariosBaseSeleccionados = [$apiario->nombre];
        }

        return view('colmenas.index', compact(
            'apiario',
            'colmenasPorApiarioBase',
            'apiariosBaseSeleccionados',
            'mostrarTitulos'
        ));
    }

    public function create(Apiario $apiario)
    {
        return view('colmenas.create', compact('apiario'));
    }

    public function store(Request $request, Apiario $apiario)
    {
        $data = $request->validate([
            'color_etiqueta' => 'required|string|max:20',
            'numero' => 'required|string|max:10',
            'estado_inicial' => 'nullable|string|max:50',
            'numero_marcos' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $codigo = (string) Str::uuid();
        $data['codigo_qr'] = $codigo;
        $data['apiario_id'] = $apiario->id;

        $colmena = Colmena::create($data);

        // QR gratuito vía API externa
        // (se muestra en tooltip en el index, no se guarda en disco)

        return redirect()->route('colmenas.index', $apiario->id)
            ->with('success', 'Colmena creada correctamente.');
    }

    public function show(Apiario $apiario, Colmena $colmena)
    {
        if (!auth()->check()) {
            // Si NO está autenticado, redirige a la vista pública
            return redirect()->route('colmenas.public', ['colmena' => $colmena->id]);
        }
        // 1) PCC del sistema experto (solo 1,2,6,7)
        $pccs = $colmena->sistemaExpertos()
                        ->with(['desarrolloCria','calidadReina','indiceCosecha','preparacionInvernada'])
                        ->orderByDesc('fecha')
                        ->get();

        // 2) Últimos registros del cuaderno de campo
        $lastAlimentacion = $colmena->alimentaciones()->latest('created_at')->first();
        $lastVarroa       = $colmena->varroas()->latest('created_at')->first();
        $lastNosemosis    = $colmena->nosemosis()->latest('created_at')->first();

        return view('colmenas.show', compact(
            'apiario','colmena','pccs',
            'lastAlimentacion','lastVarroa','lastNosemosis'
        ));
    }

    public function historial(Apiario $apiario, Colmena $colmena)
    {
        $movimientos = $colmena->movimientos()
            ->with(['apiarioOrigen', 'apiarioDestino'])
            ->orderByDesc('fecha_movimiento') // Más reciente primero para la vista
            ->get();

        return view('colmenas.historial', compact('apiario', 'colmena', 'movimientos'));
    }

    public function edit(Apiario $apiario, Colmena $colmena)
    {
        return view('colmenas.edit', compact('apiario', 'colmena'));
    }
    public function update(Request $request, Apiario $apiario, Colmena $colmena)
    {
        $data = $request->validate([
            'color_etiqueta' => 'required|string',
            'numero' => 'required|string',
            'estado_inicial' => 'nullable|string',
            'numero_marcos' => 'nullable|integer',
            'observaciones' => 'nullable|string',
        ]);

        $colmena->update($data);

        return redirect()->route('colmenas.show', [$apiario->id, $colmena->id])->with('success', 'Colmena actualizada.');
    }

    public function destroy(Apiario $apiario, Colmena $colmena)
    {
        $colmena->delete();

        return redirect()->route('colmenas.index', $apiario->id)->with('success', 'Colmena eliminada.');
    }

    public function exportHistorial(Apiario $apiario, Colmena $colmena)
    {
        $movimientos = $colmena->movimientos()
            ->with(['apiarioOrigen', 'apiarioDestino'])
            ->orderByDesc('fecha_movimiento')
            ->get();

        // Estadísticas (igual que en historial web)
        $apiariosVisitados = $movimientos->pluck('apiarioDestino.nombre')->unique()->filter()->count();
        $tiempoEnActual = $movimientos->isNotEmpty() ?
            \Carbon\Carbon::parse($movimientos->first()->fecha_movimiento)->diffForHumans(null, false, false, 2) : 'N/A';

        // Convertir a español (igual que en historial web)
        $tiempoEnActual = str_replace([
            'seconds',
            'second',
            'minutes',
            'minute',
            'hours',
            'hour',
            'days',
            'day',
            'weeks',
            'week',
            'months',
            'month',
            'years',
            'year',
            'ago',
            'from now',
            'before',
            'after'
        ], [
            'segundos',
            'segundo',
            'minutos',
            'minuto',
            'horas',
            'hora',
            'días',
            'día',
            'semanas',
            'semana',
            'meses',
            'mes',
            'años',
            'año',
            '',
            'en',
            'antes',
            'después'
        ], $tiempoEnActual);

        // Apiario base (igual que en historial web)
        $apiarioBase = null;
        if ($movimientos->isNotEmpty()) {
            $primerMovimiento = $movimientos->last();
            $apiarioBase = $primerMovimiento->apiarioOrigen;
            if (!$apiarioBase) {
                $apiarioBase = $primerMovimiento->apiarioDestino;
            }
        } else {
            $apiarioBase = $colmena->apiario;
        }

        // Resumen de ubicaciones (igual que en historial web)
        $ubicacionesConTiempo = $movimientos->groupBy('apiarioDestino.nombre')->map(function ($group, $nombre) {
            $movs = $group->sortBy('fecha_movimiento');
            $primer = $movs->first();
            $ultimo = $movs->last();

            return [
                'nombre' => $nombre ?: 'Sin especificar',
                'visitas' => $group->count(),
                'primera_visita' => $primer->fecha_movimiento,
                'ultima_visita' => $ultimo->fecha_movimiento,
                'tiempo_total' => abs((int) \Carbon\Carbon::parse($primer->fecha_movimiento)->diffInDays(\Carbon\Carbon::parse($ultimo->fecha_movimiento)))
            ];
        });

        $data = [
            'colmena' => $colmena,
            'apiario' => $apiario,
            'movimientos' => $movimientos,
            'apiariosVisitados' => $apiariosVisitados,
            'tiempoEnActual' => $tiempoEnActual,
            'apiarioBase' => $apiarioBase,
            'ubicacionesConTiempo' => $ubicacionesConTiempo,
            'fechaGeneracion' => now()->format('d/m/Y H:i')
        ];

        $pdf = Pdf::loadView('documents.movimiento-colmenas', $data);
        $filename = "historial_colmena_{$colmena->numero}_{$apiario->nombre}_" . now()->format('Y-m-d') . ".pdf";

        return $pdf->download($filename);
    }

    public function publicView(Colmena $colmena)
    {
        $apiario = $colmena->apiario;
        $pccs = \App\Models\SistemaExperto::where('colmena_id', $colmena->id)
            ->with([
                'desarrolloCria',
                'calidadReina',
                'estadoNutricional',
                'presenciaVarroa',
                'presenciaNosemosis',
                'indiceCosecha',
                'preparacionInvernada',
            ])
            ->orderByDesc('fecha')
            ->get();

        return view('colmenas.public', compact('colmena', 'apiario', 'pccs'));
    }
}