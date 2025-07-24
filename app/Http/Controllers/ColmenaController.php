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
use Illuminate\Support\Facades\DB;

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
            return redirect()->route('colmenas.public', ['colmena' => $colmena->id]);
        }

        // 1) Cada tabla hija usando colmena_id
        $pcc1 = DB::table('desarrollo_cria')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc2 = DB::table('calidad_reina')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc3 = DB::table('estado_nutricional')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc4 = DB::table('presencia_varroa')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc5 = DB::table('presencia_nosemosis')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc6 = DB::table('indice_cosecha')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc7 = DB::table('preparacion_invernada')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        // 2) Estadísticas
        // Contamos cuántos registros hay en total en esas tablas
        $pccCount = collect([$pcc1, $pcc2, $pcc3, $pcc4, $pcc5, $pcc6, $pcc7])
            ->filter()
            ->count();

        // La fecha más reciente entre todas las tablas
        $lastFecha = collect([
            optional($pcc1)->fecha ?? optional($pcc1)->created_at,
            optional($pcc2)->fecha ?? optional($pcc2)->created_at,
            optional($pcc3)->fecha ?? optional($pcc3)->created_at,
            optional($pcc4)->fecha ?? optional($pcc4)->created_at,
            optional($pcc5)->fecha ?? optional($pcc5)->created_at,
            optional($pcc6)->fecha ?? optional($pcc6)->created_at,
            optional($pcc7)->fecha ?? optional($pcc7)->created_at,
        ])->filter()->max();

        return view('colmenas.show', compact(
            'apiario',
            'colmena',
            'pcc1',
            'pcc2',
            'pcc3',
            'pcc4',
            'pcc5',
            'pcc6',
            'pcc7',
            'pccCount',
            'lastFecha'
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

        // 1) Cada tabla hija usando colmena_id
        $pcc1 = DB::table('desarrollo_cria')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc2 = DB::table('calidad_reina')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc3 = DB::table('estado_nutricional')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc4 = DB::table('presencia_varroa')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc5 = DB::table('presencia_nosemosis')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc6 = DB::table('indice_cosecha')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pcc7 = DB::table('preparacion_invernada')
            ->where('colmena_id', $colmena->id)
            ->latest('id')
            ->first();

        $pccCount = collect([$pcc1, $pcc2, $pcc3, $pcc4, $pcc5, $pcc6, $pcc7])
            ->filter()
            ->count();

        // La fecha más reciente entre todas las tablas
        $lastFecha = collect([
            optional($pcc1)->fecha ?? optional($pcc1)->created_at,
            optional($pcc2)->fecha ?? optional($pcc2)->created_at,
            optional($pcc3)->fecha ?? optional($pcc3)->created_at,
            optional($pcc4)->fecha ?? optional($pcc4)->created_at,
            optional($pcc5)->fecha ?? optional($pcc5)->created_at,
            optional($pcc6)->fecha ?? optional($pcc6)->created_at,
            optional($pcc7)->fecha ?? optional($pcc7)->created_at,
        ])->filter()->max();

        return view('colmenas.public', compact(
            'colmena',
            'apiario',
            'pcc1',
            'pcc2',
            'pcc3',
            'pcc4',
            'pcc5',
            'pcc6',
            'pcc7',
            'pccCount',
            'lastFecha'
        ));
    }

    public function historicas(Apiario $apiario)
    {
        // 1) Traigo todos los movimientos donde este apiario fue destino
        $movimientos = MovimientoColmena::with(['colmena', 'apiarioOrigen'])
            ->where('apiario_destino_id', $apiario->id)
            ->orderBy('fecha_movimiento')
            ->get();

        // 2) Agrupo por el nombre de su apiario de origen
        $colmenasPorOrigen = $movimientos
            ->groupBy(fn($mov) => $mov->apiarioOrigen?->nombre ?? 'Sin especificar')
            ->map(
                fn($grupo) => $grupo
                    ->pluck('colmena')
                    ->sortBy('numero')
                    ->values()
            );

        return view('colmenas.historicas', compact('apiario', 'colmenasPorOrigen'));
    }

    /*
    public function exportHistoricas(Apiario $apiario)
    {
        $movimientos = MovimientoColmena::where('apiario_destino_id', $apiario->id)
            ->orWhere('apiario_origen_id', $apiario->id)
            ->with(['apiarioOrigen', 'apiarioDestino', 'colmena'])
            ->orderByDesc('fecha_movimiento')
            ->get();

        // 2) Agrupo por cada colmena los movimientos que encontró
        $colmenas = $movimientos
            ->groupBy(fn($mov) => $mov->colmena->id)
            ->map(function($movs){
                $col = $movs->first()->colmena;      // modelo Colmena
                $col->movimientos = $movs;           // le inyecto la colección de movimientos
                return $col;
            })
            ->sortBy('numero')                       // opcional: ordenarlas por número
            ->values();

        // 3) Genero el PDF con la vista
        $pdf = Pdf::loadView('documents.historial-apiario', [
            'apiario'         => $apiario,
            'colmenas'        => $colmenas,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
        ]);

        $filename = "historial_apiario_{$apiario->nombre}_" . now()->format('Y-m-d') . ".pdf";
        return $pdf->download($filename);
    }
    */

    public function exportHistoricas(Apiario $apiario)
    {
        $movimientos = MovimientoColmena::where('apiario_destino_id', $apiario->id)
            ->orWhere('apiario_origen_id', $apiario->id)
            ->with(['apiarioOrigen', 'apiarioDestino', 'colmena'])
            ->orderByDesc('fecha_movimiento')
            ->get();

        $colmenas = $movimientos
            ->groupBy(fn($mov) => $mov->colmena->id)
            ->map(function ($movs) {
                $col = $movs->first()->colmena;
                $col->movimientos = $movs;
                return $col;
            })
            ->sortBy('numero')
            ->values();

        // 🧩 Tomamos datos del primer movimiento
        $mov = $movimientos->first();

        $pdf = Pdf::loadView('documents.historial-apiario', [
            'apiario' => $apiario,
            'colmenas' => $colmenas,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            // NUEVOS CAMPOS:
            'tipo_movimiento' => $mov->tipo_movimiento ?? 'traslado',
            'fecha_inicio_mov' => $mov->fecha_inicio_mov ?? $mov->fecha_movimiento,
            'fecha_termino_mov' => $mov->fecha_termino_mov ?? $mov->fecha_movimiento,
            'motivo_movimiento' => $mov->motivo_movimiento ?? null,
            'transportista' => $mov->transportista ?? '—',
            'vehiculo' => $mov->vehiculo ?? '—',
            'cultivo' => $mov->cultivo ?? null,
            'periodo_floracion' => $mov->periodo_floracion ?? null,
            'hectareas' => $mov->hectareas ?? null,
            'region_destino' => optional($apiario->comuna->region)->nombre ?? '—',
            'comuna_destino' => optional($apiario->comuna)->nombre ?? '—',
            'coordenadas_destino' => $apiario->latitud . ', ' . $apiario->longitud,
            'apicultor_nombre' => auth()->user()->name,
            'apicultor_rut' => auth()->user()->rut ?? '—',
            'registro_nacional' => auth()->user()->numero_registro ?? '—',
        ]);

        $filename = "historial_apiario_{$apiario->nombre}_" . now()->format('Y-m-d') . ".pdf";
        return $pdf->download($filename);
    }

    public function updateColor(Request $request, $apiarioId, $colmenaId)
    {
        $color = $request->color_etiqueta === 'personalizado'
            ? $request->color_personalizado
            : $request->color_etiqueta;

        $request->validate([
            'color_etiqueta' => 'required|string',
            'color_personalizado' => 'nullable|string',
        ]);

        $colmena = Colmena::findOrFail($colmenaId);
        $colmena->color_etiqueta = $color;
        $colmena->save();

        return redirect()->route('colmenas.show', [$apiarioId, $colmenaId])
            ->with('success', 'Color actualizado correctamente');
    }

}