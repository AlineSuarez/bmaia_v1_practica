<?php
namespace App\Http\Controllers;

use App\Models\Apiario;
use App\Models\Visita;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

class VisitaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $apiarios = Apiario::where('user_id', auth()->id())->get();
        return view('visitas.index', compact('apiarios','user'));
    }

    public function show(Apiario $apiario)
    {
        $visitas = $apiario->visitas()->latest()->get();
        return view('visitas.show', compact('apiario', 'visitas'));
    }

    public function create($id_apiario)
    {
        $user = auth()->user();
        $apiario = Apiario::where('user_id', auth()->id())
        ->where('id', $id_apiario)
        ->first();

    // Verificar si el apiario existe
    if (!$apiario) {
        abort(404, 'Apiario no encontrado.');
    }
    return view('visitas.create', compact('apiario','user'));
    }

    public function createMedicamentos($id_apiario)
    {
        $user = auth()->user();
        $apiario = Apiario::where('user_id', auth()->id())->where('id', $id_apiario)->firstOrFail();
    return view('visitas.create2', compact('apiario','user'));
    }

    public function createGeneral($id_apiario)
    {
        $user = auth()->user();
        $apiario = Apiario::where('user_id', auth()->id())->where('id', $id_apiario)->firstOrFail();
        $visita = Visita::where('apiario_id', $id_apiario)->where('tipo_visita', 'Visita General')->first();
    return view('visitas.create1', compact('apiario','visita','user'));
    }

    public function store(Request $request, Apiario $apiario)
    {
        // Lógica para guardar el registro de inspección de apiario (visitas.create)
        $validated = $request->validate([
            'fecha_inspeccion' => 'required|date',
            'num_colmenas_totales' => 'required|integer',
            'num_colmenas_activas' => 'required|integer',
            'num_colmenas_enfermas' => 'required|integer',
            'num_colmenas_muertas' => 'required|integer',
            'num_colmenas_inspeccionadas' => 'required|integer',
            'flujo_nectar_polen' => 'required|string',
            'nombre_revisor_apiario' => 'required|string',
            'sospecha_enfermedad' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        Visita::create([
            'apiario_id' => $apiario->id,
            'user_id' => auth()->id(),
            'fecha_visita' => $validated['fecha_inspeccion'],
            'num_colmenas_totales' => $validated['num_colmenas_totales'],
            'num_colmenas_activas' => $validated['num_colmenas_activas'],
            'num_colmenas_enfermas' => $validated['num_colmenas_enfermas'],
            'num_colmenas_muertas' => $validated['num_colmenas_muertas'],
            'num_colmenas_inspeccionadas' => $validated['num_colmenas_inspeccionadas'],
            'flujo_nectar_polen' => $validated['flujo_nectar_polen'],
            'nombre_revisor_apiario' => $validated['nombre_revisor_apiario'],
            'sospecha_enfermedad' => $validated['sospecha_enfermedad'],
            'observaciones' => $validated['observaciones'],
            'tipo_visita' => 'Inspección de Visita',
        ]);
        return redirect()->route('visitas')->with('success', 'Registro de Inspección guardado correctamente.');
    }

    public function storeMedicamentos(Request $request, Apiario $apiario)
    {
        // Lógica para guardar el registro de uso de medicamentos (visitas.create2)
        $validated = $request->validate([
            'fecha' => 'required|date',
            'num_colmenas_tratadas' => 'required|integer',
            'motivo_tratamiento' => 'required|string',
            'nombre_comercial_medicamento' => 'required|string',
            'principio_activo_medicamento' => 'required|string',
            'periodo_resguardo' => 'required|string',
            'responsable' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        Visita::create([
            'apiario_id' => $apiario->id,
            'user_id' => auth()->id(),
            'fecha_visita' => $validated['fecha'],
            'num_colmenas_tratadas' => $validated['num_colmenas_tratadas'],
            'motivo_tratamiento' => $validated['motivo_tratamiento'],
            'nombre_comercial_medicamento' => $validated['nombre_comercial_medicamento'],
            'principio_activo_medicamento' => $validated['principio_activo_medicamento'],
            'periodo_resguardo' => $validated['periodo_resguardo'],
            'responsable' => $validated['responsable'],
            'observaciones' => $validated['observaciones'],
            'tipo_visita' => 'Uso de Medicamentos',
        ]);
        return redirect()->route('visitas')->with('success', 'Registro de Uso de Medicamentos guardado correctamente.');
    }

    public function storeGeneral(Request $request, Apiario $apiario)
    {
        // Lógica para guardar el registro de visita general (visitas.create1)
        $validated = $request->validate([
            'fecha' => 'required|date',
            'motivo' => 'required|string',
        ]);

        Visita::create([
            'apiario_id' => $apiario->id,
            'user_id' => auth()->id(),
            'fecha_visita' => $validated['fecha'],
            'motivo' => $validated['motivo'],
            'tipo_visita' => 'Visita General',
        ]);
        return redirect()->route('visitas')->with('success', 'Registro de Visita General guardado correctamente.');
    }

    public function showHistorial($apiarioId)
    {
        // Obtener el apiario y sus visitas
        $apiario = Apiario::with('visitas')->findOrFail($apiarioId);
        // Retornar la vista de historial con los datos del apiario y sus visitas
        return view('visitas.historial', compact('apiario'));
    }
}
