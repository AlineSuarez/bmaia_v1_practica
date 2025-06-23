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
        $apiarioTemp = Apiario::findOrFail($id);

        if ($apiarioTemp->tipo_apiario !== 'trashumante') {
            return response()->json(['error' => 'Solo apiarios temporales pueden archivarse.'], 422);
        }

        return DB::transaction(function () use ($apiarioTemp) {
            // 1) Obtener todas las colmenas que todavía estén en este apiario temporal
            $colmenas = Colmena::where('apiario_id', $apiarioTemp->id)->get();

            foreach ($colmenas as $colmena) {
                // 2) Buscar el último traslado para saber cuál fue el apiario padre original
                $ultimoTraslado = $colmena->movimientos()
                    ->where('tipo_movimiento', 'traslado')
                    ->latest('fecha_movimiento')
                    ->first();

                if (! $ultimoTraslado) {
                    continue;
                }

                // 3) Crear el registro de retorno
                MovimientoColmena::create([
                    'colmena_id'         => $colmena->id,
                    'apiario_origen_id'  => $apiarioTemp->id,
                    'apiario_destino_id' => $ultimoTraslado->apiario_origen_id,
                    'tipo_movimiento'    => 'retorno',
                    'fecha_movimiento'   => now(),
                    'fecha_inicio_mov'   => now()->subDays(1),
                    'fecha_termino_mov'  => now(),
                    'motivo_movimiento'  => 'Retorno por archivado manual',
                    'transportista'      => 'Sistema',
                    'vehiculo'           => 'Sistema',
                ]);

                // 4) Reasignar físicamente la colmena de vuelta al apiario padre
                $apiarioPadre = Apiario::find($ultimoTraslado->apiario_origen_id);

                // → Incrementar en +1 el num_colmenas en el apiario padre
                if ($apiarioPadre) {
                    $apiarioPadre->increment('num_colmenas', 1);
                }

                // → Actualizar la FK en la colmena
                $colmena->update(['apiario_id' => $ultimoTraslado->apiario_origen_id]);
            }

            // 5) Ahora que ya no quedan colmenas dentro del apiario temporal, dejamos num_colmenas = 0
            $apiarioTemp->update([
                'num_colmenas' => 0,
                'activo'       => 0,
            ]);

            return response()->json(['message' => 'Apiario temporal archivado correctamente']);
        });
    }



    public function archivarMultiples(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return redirect()->route('apiarios')->with('error', 'No se enviaron apiarios para archivar.');
        }

        DB::transaction(function() use ($ids) {
            foreach ($ids as $id) {
                $apiarioTemp = Apiario::where('id', $id)
                                    ->where('tipo_apiario', 'trashumante')
                                    ->where('es_temporal', true)
                                    ->where('activo', 1)
                                    ->first();

                if (! $apiarioTemp) {
                    continue; // solo procesamos temporales activos
                }

                // Mismo bloque que en archivar():
                $colmenas = Colmena::where('apiario_id', $apiarioTemp->id)->get();
                foreach ($colmenas as $colmena) {
                    $ultimoTraslado = $colmena->movimientos()
                        ->where('tipo_movimiento', 'traslado')
                        ->latest('fecha_movimiento')
                        ->first();

                    if (! $ultimoTraslado) {
                        continue;
                    }

                    MovimientoColmena::create([
                        'colmena_id'         => $colmena->id,
                        'apiario_origen_id'  => $apiarioTemp->id,
                        'apiario_destino_id' => $ultimoTraslado->apiario_origen_id,
                        'tipo_movimiento'    => 'retorno',
                        'fecha_movimiento'   => now(),
                        'fecha_inicio_mov'   => now()->subDays(1),
                        'fecha_termino_mov'  => now(),
                        'motivo_movimiento'  => 'Retorno por archivado múltiple',
                        'transportista'      => 'Sistema',
                        'vehiculo'           => 'Sistema',
                    ]);

                    // Incrementar en 1 el num_colmenas del apiario padre
                    $apiarioPadre = Apiario::find($ultimoTraslado->apiario_origen_id);
                    if ($apiarioPadre) {
                        $apiarioPadre->increment('num_colmenas', 1);
                    }

                    $colmena->update(['apiario_id' => $ultimoTraslado->apiario_origen_id]);
                }

                // Finalmente, pongo el temporal en 0 colmenas y lo inactiveo
                $apiarioTemp->update([
                    'num_colmenas' => 0,
                    'activo'       => 0,
                ]);
            }
        });

        return redirect()->route('apiarios')->with('success', 'Apiarios temporales archivados correctamente.');
    }
    
    public function showColmenas($apiarioId)
    {
        $apiario = Apiario::findOrFail($apiarioId);
        if ($apiario->tipo_apiario !== 'trashumante' || !$apiario->es_temporal) {
            abort(404, 'No es un apiario temporal válido.');
        }

        // Traigo solo los movimientos de traslado hacia este temporal
        $movs = MovimientoColmena::with(['apiarioOrigen','colmena'])
            ->where('apiario_destino_id', $apiario->id)
            ->where('tipo_movimiento', 'traslado')
            ->get();

        // Agrupo las colmenas por el nombre de su apiario de origen
        $colmenasPorApiarioBase = $movs
            ->groupBy(fn($m) => optional($m->apiarioOrigen)->nombre ?: 'Sin apiario base')
            ->map(fn($grupo) => $grupo->pluck('colmena'));

        // Lista ordenada de nombres de apiarios
        $apiariosBaseSeleccionados = $colmenasPorApiarioBase->keys()->all();

        // OJO: aquí devolvemos la vista de índice de colmenas
        return view('colmenas.index', compact(
            'apiario',
            'apiariosBaseSeleccionados',
            'colmenasPorApiarioBase'
        ));
    }


}
