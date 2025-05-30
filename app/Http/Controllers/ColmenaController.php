<?php
namespace App\Http\Controllers;

use App\Models\Apiario;
use App\Models\Colmena;
use Illuminate\Http\Request;

class ColmenaController extends Controller
{
    public function index(Apiario $apiario)
    {
        $colmenas = $apiario->colmenas;
        return view('colmenas.index', compact('apiario', 'colmenas'));
    }

    public function create(Apiario $apiario)
    {
        return view('colmenas.create', compact('apiario'));
    }

    public function store(Request $request, Apiario $apiario)
    {
        $validated = $request->validate([
            'codigo_qr' => 'required|unique:colmenas',
            'color_etiqueta' => 'required|string',
            'numero' => 'required|string',
        ]);

        $apiario->colmenas()->create($validated);

        return redirect()->route('colmenas.index', $apiario->id)->with('success', 'Colmena creada');
    }

    public function show(Apiario $apiario, Colmena $colmena)
    {
        return view('colmenas.show', compact('apiario', 'colmena'));
    }
    
    public function historial(Apiario $apiario, Colmena $colmena)
    {
        $movimientos = $colmena->movimientos()->with(['origen', 'destino'])->orderByDesc('fecha_movimiento')->get();
        return view('colmenas.historial', compact('colmena', 'apiario', 'movimientos'));
    }
}