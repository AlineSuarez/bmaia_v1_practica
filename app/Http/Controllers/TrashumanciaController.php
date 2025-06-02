<?php
namespace App\Http\Controllers;

use App\Models\Apiario;
use App\Models\Colmena;
use App\Models\MovimientoColmena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TrashumanciaController extends Controller
{
    public function create(Request $request)
    {
        $ids  = explode(',', $request->query('apiarios',''));
        $tipo = $request->query('tipo','traslado');
        $apiariosData = Apiario::with('colmenas')->whereIn('id', $ids)->get();
        return view('apiarios.create-temporal', compact('apiariosData','tipo'));
    }

    public function store(Request $request)
    {
        // 1) validar todos los campos que vienen del wizard
        $data = $request->validate([
            'apiarios_base'        => 'required|array|min:1',
            'apiarios_base.*'      => 'exists:apiarios,id',
            'nombre'               => 'required|string|max:255',
            'region_id'         => 'required|integer|exists:regiones,id',
            'comuna_id'         => 'required|integer|exists:comunas,id',

            'colmenas'           => 'nullable|array',
            'colmenas'           => 'nullable|array',
            'colmenas.*'         => 'integer|exists:colmenas,id',

            
            'fecha_inicio_mov'     => 'required|date',
            'fecha_termino_mov'    => 'required|date|after_or_equal:fecha_inicio_mov',
            'motivo_movimiento'    => 'required|in:Producción,Polinización',

            'apicultor_nombre'        => 'nullable|string|max:255',
            'apicultor_rut'        => 'nullable|string|max:255',
            'registro_nacional'        => 'nullable|string|max:255',

            'cultivo' => 'nullable|string|max:255',
            'periodo_floracion' => 'nullable|string|max:255',
            'hectareas' => 'nullable|integer|min:1',

            'transportista_nombre'        => 'nullable|string|max:255',
            'transportista_rut'        => 'nullable|string|max:255',
            'vehiculo_patente'             => 'nullable|string|max:50',

            'destino_region_id' => 'required|integer|exists:regiones,id',
            'destino_comuna_id' => 'required|integer|exists:comunas,id',
            'coordenadas_destino' => 'nullable|string|max:255',

        ]);

        return DB::transaction(function() use($data) {
            $user = auth()->user();
            $colmenasAReasignar = []; // este array contendrá los IDs reales de cada colmena
            
            if (!empty($data['colmenas']) && is_array($data['colmenas'])) {
                foreach ($data['colmenas'] as $apiarioBaseId => $idsColmenas) {
                    foreach ($idsColmenas as $idColmena) {
                        $colmena = Colmena::find($idColmena);
                        if ($colmena && $colmena->apiario_id == $apiarioBaseId) {
                            $colmenasAReasignar[] = $colmena->id;
                        }
                    }
                }
            }

            $apiarioTemp = Apiario::create([
                'user_id'              => auth()->id(),
                'nombre'               => $data['nombre'],
                'tipo_apiario'         => 'trashumante',
                'temporada_produccion' => now()->year,
                'num_colmenas' => $colmenasAReasignar->count(),
                'activo'               => 1,
                'es_temporal'          => true,
                'region_id' => $data['destino_region_id'],    
                'comuna_id' => $data['destino_comuna_id'],
                'created_by'           => $user->id,
                'updated_by'           => $user->id
            ]);
            if (!empty($data['colmenas']) && is_array($data['colmenas'])) {
                foreach ($data['colmenas'] as $apiarioBaseId => $listaDeIndices) {
                   $colmenasAReasignar = $data['colmenas'] ?? [];
                }
            }
            // Actualizo contador
            $apiarioTemp->update([
                'num_colmenas' => count($colmenasAReasignar)
            ]);

            foreach ($colmenasAReasignar as $colmenaId) {
                $colmena = Colmena::find($colmenaId);

                // 1) Crear registro en movimientos_colmenas
                MovimientoColmena::create([
                    'colmena_id'          => $colmena->id,
                    'apiario_origen_id'   => $colmena->apiario_id,
                    'apiario_destino_id'  => $apiarioTemp->id,
                    'tipo_movimiento'     => 'traslado',
                    'fecha_movimiento'    => now(),
                    'fecha_inicio_mov'    => $data['fecha_inicio_mov'],
                    'fecha_termino_mov'   => $data['fecha_termino_mov'],
                    'motivo_movimiento'   => $data['motivo_movimiento'],
                    'observaciones'       => null,
                    'transportista'       => $data['transportista_nombre'] ?? null,
                    'vehiculo'            => $data['vehiculo_patente']   ?? null,
                ]);

                // 2) Reasignar la colmena físicamente en su tabla (cambiar apiario_id)
                $colmena->update(['apiario_id' => $apiarioTemp->id]);
            }
            return redirect()->route('apiarios.index')->with('success','Apiario temporal creado y movimientos registrados correctamente');
        });
    }



    

    public function archivar($id)
    {
        $apiario = Apiario::findOrFail($id);

        if ($apiario->tipo_apiario !== 'trashumante') {
            return response()->json(['error' => 'Solo apiarios temporales pueden archivarse.'], 422);
        }

        return DB::transaction(function () use ($apiario) {
            $colmenas = Colmena::where('apiario_id', $apiario->id)->get();

            foreach ($colmenas as $colmena) {
                $ultimo = $colmena->movimientos()
                    ->where('apiario_destino_id', $apiario->id)
                    ->latest()
                    ->first();

                MovimientoColmena::create([
                    'colmena_id' => $colmena->id,
                    'apiario_origen_id' => $apiario->id,
                    'apiario_destino_id' => $ultimo->apiario_origen_id,
                    'tipo_movimiento' => 'retorno',
                    'fecha_movimiento' => now(),
                    'fecha_inicio_mov' => now()->subDays(1),
                    'fecha_termino_mov' => now(),
                    'motivo_movimiento' => 'Retorno por cierre del apiario temporal',
                    'observaciones' => 'Automático desde sistema',
                    'transportista' => 'Sistema',
                    'vehiculo' => 'Sistema',
                ]);

                $colmena->update(['apiario_id' => $ultimo->apiario_origen_id]);
            }

            if ($apiario->debeArchivarse()) {
                $apiario->update(['activo' => 0]);
            }

            return response()->json(['message' => 'Apiario temporal archivado']);
        });
    }
}
