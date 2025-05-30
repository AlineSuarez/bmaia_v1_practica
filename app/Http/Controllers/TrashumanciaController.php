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
    /*
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'               => 'required|string',
            'apiarios_base'        => 'required|array|min:1',
            'apiarios_base.*'   => 'exists:apiarios,id',
            'region_id'         => 'required|integer|exists:regions,id',
            'comuna_id'         => 'required|integer|exists:comunas,id',
            'cultivo'              => 'nullable|string',
            'transportista_nombre' => 'nullable|string',
            'vehiculo_patente'     => 'nullable|string',
        ]);

        return DB::transaction(function() use($data) {
            $user = auth()->user();

            // 1) Crear apiario temporal con región y comuna
            $apiarioTemp = Apiario::create([
                'user_id'              => $user->id,
                'nombre'               => $data['nombre'],
                'tipo_apiario'         => 'trashumante',
                'temporada_produccion' => date('Y'),
                'num_colmenas'         => 0,
                'activo'               => 1,
                'es_temporal'          => true,
                'region_id'           => $request->region_destino,
                'comuna_id'           => $request->comuna_destino,
            ]);

            // 3) Traemos todas las colmenas de los apiarios base
            $colmenas = Colmena::whereIn('apiario_id', $data['apiarios_base'])->get();

            // Actualizamos el contador de colmenas
            $apiarioTemp->update([ 'num_colmenas' => $colmenas->count() ]);

            // 4) Por cada colmena, creamos un MovimientoColmena completo
            foreach ($colmenas as $colmena) {
                MovimientoColmena::create([
                    'colmena_id'         => $colmena->id,
                    'apiario_origen_id'  => $colmena->apiario_id,
                    'apiario_destino_id' => $apiarioTemp->id,
                    'tipo_movimiento'    => 'traslado',
                    'fecha_movimiento'   => now(),
                    'fecha_inicio_mov'   => now(),
                    'cultivo'            => $data['cultivo'] ?? null,
                    'motivo_movimiento'  => 'Trashumancia iniciada',
                    'transportista'      => $data['transportista_nombre'],
                    'vehiculo'           => $data['vehiculo_patente'],
                ]);
                // finalmente reasignamos la colmena
                $colmena->update(['apiario_id' => $apiarioTemp->id]);
            }

            return redirect()
                ->route('apiarios')
                ->with('success','Apiario temporal creado correctamente.');
        });
    }
    */

    public function create(Request $request)
    {
        // recibe ?apiarios=1,2,3&tipo=traslado
        $ids = explode(',', $request->query('apiarios',''));
        $tipo = $request->query('tipo','traslado');
        $apiarios = Apiario::whereIn('id',$ids)->get();
        return view('apiarios.create-temporal', compact('apiarios','tipo'));
    }

    public function store(Request $request)
    {
        // 1) validar todos los campos que vienen del wizard
        $data = $request->validate([
            'apiarios_base'        => 'required|array|min:1',
            'apiarios_base.*'      => 'exists:apiarios,id',
            'nombre'               => 'required|string|max:255',
            'colmenas_ids'        => 'nullable|array',
            'fecha_inicio_mov'     => 'required|date',
            'fecha_termino_mov'    => 'required|date|after_or_equal:fecha_inicio_mov',
            'motivo_movimiento'    => 'required|in:Producción,Polinización',
            'transportista'        => 'nullable|string|max:255',
            'rut_transportista'    => 'nullable|string|max:20',
            'vehiculo'             => 'nullable|string|max:50',

        ]);

        return DB::transaction(function() use($data) {
            $user = auth()->user();
            $apiarioTemp = Apiario::create([
                'user_id'              => auth()->id(),
                'nombre'               => $data['nombre'],
                'tipo_apiario'         => 'trashumante',
                'temporada_produccion' => now()->year,
                'num_colmenas'         => 0,            // lo actualizarás luego
                'activo'               => 1,
                'es_temporal'          => true,
            ]);

            $colmenas = collect(json_decode($data['colmenas_ids'],true));

            // Actualizo contador
            $apiarioTemp->update(['num_colmenas' => $colmenas->count()]);

            // Registro movimientos y reasigno
            $colmenas->each(function($colId) use($apiarioTemp, $data) {
                $col = Colmena::findOrFail($colId);

                MovimientoColmena::create([
                    'colmena_id'         => $col->id,
                    'apiario_origen_id'  => $col->apiario_id,
                    'apiario_destino_id' => $apiarioTemp->id,
                    'tipo_movimiento'    => 'traslado',
                    'fecha_movimiento'   => now(),
                    'fecha_inicio_mov'   => $data['fecha_inicio_mov'],
                    'fecha_termino_mov'  => $data['fecha_termino_mov'],
                    'motivo_movimiento'  => $data['motivo_movimiento'],
                    'transportista'      => $data['transportista'],
                    'vehiculo'           => $data['vehiculo'],
                ]);

                $col->update(['apiario_id' => $apiarioTemp->id]);
            });

            // opcional: reasignar todas las colmenas a este apiario…
            // Colmena::whereIn('apiario_id',$data['apiarios_base'])
            //       ->update(['apiario_id'=>$apiarioTemp->id]);

            return redirect()->route('apiarios.index')->with('success','Apiario temporal y movimientos creados.');
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
