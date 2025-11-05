<?php
namespace App\Http\Controllers;

use App\Models\Apiario;
use App\Models\TareaApiario;
use Illuminate\Http\Request;

class TareaApiarioController extends Controller
{
    public function index($apiarioId)
    {
        $apiario = Apiario::with('visitas', 'colmenas')->findOrFail($apiarioId);
        $tareas = TareaApiario::where('apiario_id', $apiarioId)
                    ->orderByDesc('fecha_inicio')
                    ->get();

        return view('visitas.historial', compact('apiario', 'tareas'));
    }

    public function create($apiarioId, $id = null)
    {
        $apiario = Apiario::findOrFail($apiarioId);
        $tarea = $id ? TareaApiario::findOrFail($id) : null;

        return view('visitas.tareas_apiario.create', compact('apiario', 'tarea'));
    }

    public function store(Request $request, $apiarioId)
    {
        $validated = $request->validate([
            'categoria_tarea' => 'required|string|max:100',
            'tarea_especifica' => 'nullable|string|max:255',
            'accion_realizada' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_termino' => 'nullable|date',
            'proximo_seguimiento' => 'nullable|string|max:255',
        ]);

        $validated['apiario_id'] = $apiarioId;

        TareaApiario::create($validated);

        return redirect()->route('tareas-apiario.index', $apiarioId)
                 ->with('success', 'Tarea registrada correctamente.');
    }

    public function edit($apiarioId, $id)
    {
        $apiario = Apiario::findOrFail($apiarioId);
        $tarea = TareaApiario::findOrFail($id);
        return view('visitas.tareas_apiario.edit', compact('apiario', 'tarea'));
    }

    public function update(Request $request, $apiarioId, $id)
    {
        $tarea = TareaApiario::findOrFail($id);
        $validated = $request->validate([
            'categoria_tarea' => 'required|string|max:100',
            'tarea_especifica' => 'nullable|string|max:255',
            'accion_realizada' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_termino' => 'nullable|date',
            'proximo_seguimiento' => 'nullable|string|max:255',
        ]);

        $tarea->update($validated);
        return redirect()->route('tareas-apiario.index', $apiarioId)
                 ->with('success', 'Tarea actualizada correctamente.');

    }

    public function destroy($apiarioId, $id)
    {
        TareaApiario::findOrFail($id)->delete();
        return back()->with('success', 'Tarea eliminada.');
    }

}

