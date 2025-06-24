<?php

namespace App\Http\Controllers;

use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\MovimientoColmena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            ->orderByDesc('fecha') // o created_at
            ->get();

        return view('colmenas.show', compact('apiario', 'colmena', 'pccs'));
    }

    public function historial(Apiario $apiario, Colmena $colmena)
    {
        $movimientos = $colmena->movimientos()->with(['origen', 'destino'])->orderByDesc('fecha_movimiento')->get();
        return view('colmenas.historial', compact('colmena', 'apiario', 'movimientos'));
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

}