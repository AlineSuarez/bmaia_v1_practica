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
        // 1) Leer parámetros
        $tipo        = $request->query('tipo', 'traslado');      // 'traslado' o 'retorno'
        $apiariosStr = $request->query('apiarios', '');          // string con IDs separados por coma

        // 2) Validaciones básicas
        if (empty($apiariosStr)) {
            return redirect()->route('apiarios')
                             ->with('error', 'Debe seleccionar al menos un apiario de origen.');
        }

        $apiarioIds = explode(',', $apiariosStr);
        // 3) Obtener los apiarios base con sus colmenas (para listar colmenas disponibles)
        $apiariosData = Apiario::with('colmenas')
            ->whereIn('id', $apiarioIds)
            ->where('tipo_apiario', 'fijo') // sólo apiarios fijos/padres
            ->get();

        if ($apiariosData->isEmpty()) {
            return redirect()->route('apiarios')
                             ->with('error', 'No se encontraron los apiarios seleccionados.');
        }

        // 4) Enviar a la vista wizard, con colección de Apiarios (cada uno ya trae ->colmenas)
        return view('apiarios.create-temporal', compact('tipo', 'apiariosData'));
    }
    
    public function store(Request $request)
    {
        //dd($request->all());
        // 1) Definición de reglas de validación (ya lo tenías)
        $rules = [
            'tipo'                  => 'required|in:traslado,retorno',
            'apiarios_base'         => 'required|array|min:1',
            'apiarios_base.*'       => 'required|integer|exists:apiarios,id',
            'colmenas'              => 'required|array',             // colmenas[<apiario_id>][] 
            'colmenas.*'            => 'required|array|min:1',
            'colmenas.*.*'          => 'required|integer|exists:colmenas,id',

            'nombre'                => 'required|string|max:255',
            'fecha_inicio_mov'      => 'required|date',
            'fecha_termino_mov'     => 'required|date|after_or_equal:fecha_inicio_mov',
            'motivo_movimiento'     => 'required|in:Producción,Polinización',

            'transportista_nombre'  => 'nullable|string|max:255',
            'transportista_rut'     => 'nullable|string|max:255',
            'vehiculo_patente'      => 'nullable|string|max:50',
        ];

        // Si es traslado, obligamos región/comuna destino
        if ($request->input('tipo') === 'traslado') {
            $rules['destino_region_id']   = 'required|integer|exists:regiones,id';
            $rules['destino_comuna_id']   = 'required|integer|exists:comunas,id';
            $rules['coordenadas_destino'] = 'nullable|string|max:50'; // p.ej. "-33.0472, -71.4419"
        }

        // Si es polinización, obligamos esos campos
        if ($request->input('motivo_movimiento') === 'Polinización') {
            $rules['cultivo']           = 'required|string|max:255';
            $rules['periodo_floracion'] = 'required|string|max:255';
            $rules['hectareas']         = 'required|integer|min:1';
        }

        // 2) Validar todo
        $data = $request->validate($rules);

        // 3) Iniciar transacción para que todo falle o tenga éxito completo
        DB::transaction(function() use ($data) {
            // 3.1) Preparar datos comunes para crear el Apiario temporal
            $destinoRegionId = $data['destino_region_id'] ?? null;
            $destinoComunaId = $data['destino_comuna_id'] ?? null;

            // 3.2) Si vienen coordenadas_destino, extraer lat/lng
            $lat = null;
            $lng = null;
            if (!empty($data['coordenadas_destino'])) {
                [$lat, $lng] = array_map('trim', explode(',', $data['coordenadas_destino']));
                $lat = (float) $lat;
                $lng = (float) $lng;
            }

            // 3.3) Crear el Apiario temporal “básico”
            $apiarioTemp = Apiario::create([
                'user_id'               => auth()->id(),
                'nombre'                => $data['nombre'],
                'temporada_produccion'  => now()->year,
                'registro_sag'          => null,                 // no aplica al temporales
                'num_colmenas'          => 0,                    // se actualizará luego
                'tipo_manejo'           => null,                 // no aplica aquí
                'objetivo_produccion'   => $data['motivo_movimiento'],
                'region_id'             => $destinoRegionId,
                'comuna_id'             => $destinoComunaId,
                'latitud'               => $lat,
                'longitud'              => $lng,
                'foto'                  => null,                 // si lo necesitas, agrégalo
                // Ahora marcamos las banderas:
                'tipo_apiario'          => 'trashumante',
                'activo'                => 1,
                'es_temporal'           => 1,
            ]);

            // 3.4) Determinar qué colmenas se van a mover
            //      Recorremos cada apiario_base y tomamos las colmenas marcadas
            $colmenasIds = [];
            foreach ($data['apiarios_base'] as $apiarioBaseId) {
                if (isset($data['colmenas'][$apiarioBaseId]) && is_array($data['colmenas'][$apiarioBaseId])) {
                    foreach ($data['colmenas'][$apiarioBaseId] as $colmenaId) {
                        // Verificar que esa colmena exista y pertenezca a ese apiario de origen
                        $col = Colmena::where('id', $colmenaId)
                                      ->where('apiario_id', $apiarioBaseId)
                                      ->first();
                        if ($col) {
                            $colmenasIds[] = $col->id;
                        }
                    }
                }
            }

            // 3.5) Actualizar el conteo de colmenas en el Apiario Temporal
            $cantidadColmenas = count($colmenasIds);
            $apiarioTemp->update(['num_colmenas' => $cantidadColmenas]);

            // 3.6) Crear un registro de MovimientoColmena y reasignar físicamente
            foreach ($colmenasIds as $colId) {
                $colm = Colmena::find($colId);
                if (!$colm) {
                    continue;
                }

                MovimientoColmena::create([
                    'colmena_id'         => $colm->id,
                    'apiario_origen_id'  => $colm->apiario_id,
                    'apiario_destino_id' => $apiarioTemp->id,
                    'tipo_movimiento'    => $data['tipo'],            
                    'fecha_movimiento'   => now(),
                    'fecha_inicio_mov'   => $data['fecha_inicio_mov'],
                    'fecha_termino_mov'  => $data['fecha_termino_mov'],
                    'motivo_movimiento'  => $data['motivo_movimiento'],
                    'cultivo'            => $data['cultivo'] ?? null,
                    'periodo_floracion'  => $data['periodo_floracion'] ?? null,
                    'hectareas'          => $data['hectareas'] ?? null,
                    'transportista'      => $data['transportista_nombre'] ?? null,
                    'vehiculo'           => $data['vehiculo_patente'] ?? null,
                ]);

                $origenId = $colm->apiario_id;
                // Reasignar físicamente la colmena a este Apiario temporal
                $colm->update(['apiario_id' => $apiarioTemp->id]);
                 // Decrementar colmenas del apiario base
                $apiarioOrigen = Apiario::find($origenId);
                if ($apiarioOrigen) {
                    $apiarioOrigen->decrement('num_colmenas', 1);
                }
            }

            // 3.7) Si no se movió ninguna colmena (num_colmenas == 0), archivamos en el mismo momento
            /* NO hay que archivar aquí, porque ahora el temporal siempre arranca con activo = 1
            if ($cantidadColmenas === 0) {
                $apiarioTemp->update(['activo' => 0]);
            } */
        });

        return redirect()
            ->route('apiarios')
            ->with('success', 'Movimiento registrado y apiario temporal creado correctamente.');
    }

    public function archivar($id)
    {
        $apiario = Apiario::findOrFail($id);

        if ($apiario->tipo_apiario !== 'trashumante') {
            return response()->json(['error' => 'Solo apiarios temporales pueden archivarse.'], 422);
        }

        DB::transaction(function () use ($apiario) {
            $colmenas = Colmena::where('apiario_id', $apiario->id)->get();

            foreach ($colmenas as $colmena) {
                $ultimoTraslado = $colmena->movimientos()
                    ->where('tipo_movimiento', 'traslado')
                    ->latest('fecha_movimiento')
                    ->first();

                if (!$ultimoTraslado) {
                    continue;
                }

                // Crear movimiento de retorno
                MovimientoColmena::create([
                    'colmena_id'         => $colmena->id,
                    'apiario_origen_id'  => $apiario->id,
                    'apiario_destino_id' => $ultimoTraslado->apiario_origen_id,
                    'tipo_movimiento'    => 'retorno',
                    'fecha_movimiento'   => now(),
                    'fecha_inicio_mov'   => now()->subDay(),
                    'fecha_termino_mov'  => now(),
                    'motivo_movimiento'  => 'Retorno por archivado manual',
                    'transportista'      => 'Sistema',
                    'vehiculo'           => 'Sistema',
                    'created_by'         => auth()->id(),
                    'updated_by'         => auth()->id(),
                ]);

                // Reasignar físicamente la colmena al apiario padre
                $colmena->update(['apiario_id' => $ultimoTraslado->apiario_origen_id]);

                // Incrementar num_colmenas en el apiario padre
                $apiarioOrigen = Apiario::find($ultimoTraslado->apiario_origen_id);
                if ($apiarioOrigen) {
                    $apiarioOrigen->increment('num_colmenas', 1);
                }
            }

            // Marcar este apiario temporal como inactivo (archivado) y num_colmenas = 0
            $apiario->update([
                'activo'       => 0,
                'num_colmenas' => 0,
            ]);
        });

        return response()->json(['message' => 'Apiario temporal archivado correctamente']);
    }


    public function archivarMultiples(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('apiarios')->with('error', 'No se seleccionaron apiarios temporales.');
        }

        foreach ($ids as $id) {
            // Llamamos al método archivar para cada ID
            $this->archivar($id);
        }

        return redirect()
            ->route('apiarios')
            ->with('success', 'Apiarios temporales archivados correctamente.');
    }

}
