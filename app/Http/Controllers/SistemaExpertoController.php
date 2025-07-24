<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Apiario,
    Colmena,
    SistemaExperto,
    DesarrolloCria,
    CalidadReina,
    EstadoNutricional,
    PresenciaVarroa,
    PresenciaNosemosis,
    IndiceCosecha,
    PreparacionInvernada
};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class SistemaExpertoController extends Controller
{
    public function index()
    {
        $apiarios = Auth::user()->apiarios;
        return view('sistemaexperto.index', compact('apiarios'));
    }

    public function create(Apiario $apiario)
    {
        $ultimo = SistemaExperto::where('apiario_id', $apiario->id)
            ->with(
                'desarrolloCria',
                'calidadReina',
                'estadoNutricional',
                'presenciaVarroa',
                'presenciaNosemosis',
                'indiceCosecha',
                'preparacionInvernada'
            )
            ->latest('fecha')
            ->first();

        if ($ultimo) {
            $valores = [
                'desarrollo_cria'       => optional($ultimo->desarrolloCria)->toArray()      ?? [],
                'calidad_reina'         => optional($ultimo->calidadReina)->toArray()        ?? [],
                'estado_nutricional'    => optional($ultimo->estadoNutricional)->toArray() ?? [],
                'presencia_varroa'      => optional($ultimo->presenciaVarroa)->toArray()     ?? [],
                'presencia_nosemosis'   => optional($ultimo->presenciaNosemosis)->toArray()  ?? [],
                'indice_cosecha'        => optional($ultimo->indiceCosecha)->toArray()       ?? [],
                'preparacion_invernada' => optional($ultimo->preparacionInvernada)->toArray() ?? [],
            ];
        } else {
            // vacío
            $valores = array_fill_keys([
                'desarrollo_cria','calidad_reina','estado_nutricional',
                'presencia_varroa','presencia_nosemosis','indice_cosecha','preparacion_invernada'
            ], []);
        }

        return view('sistemaexperto.create', compact('apiario','valores'));
    }

    function tieneDatosRelevantes(array $data, array $fillable): bool {
        foreach ($fillable as $campo) {
            if (isset($data[$campo]) && $data[$campo] !== '' && $data[$campo] !== null) {
                return true;
            }
        }
        return false;
    }

    public function store(Request $request, Apiario $apiario)
    {
        // 1) Validar
        $request->validate([
            'desarrollo_cria'      => 'array',
            'calidad_reina'        => 'array',
            'estado_nutricional'   => 'array',
            'presencia_varroa'     => 'array',
            'presencia_nosemosis'  => 'array',
            'indice_cosecha'       => 'array',
            'preparacion_invernada'=> 'array',
            'calidad_reina.reemplazos_realizados' => 'nullable|array',
            'calidad_reina.reemplazos_realizados.*.fecha'  => 'nullable|date|required_with:calidad_reina.reemplazos_realizados.*.motivo',
            'calidad_reina.reemplazos_realizados.*.motivo' => 'nullable|string|required_with:calidad_reina.reemplazos_realizados.*.fecha',
            'estado_nutricional.objetivo' => 'nullable|in:estimulacion,mantencion',
        ]);

        // 2) Filtrar los arrays de entrada
        $dcData = array_filter($request->input('desarrollo_cria', []), fn($v) => $v!=='');
        $crData = array_filter($request->input('calidad_reina', []), fn($v) => $v!=='');
        $enData = array_filter($request->input('estado_nutricional', []), fn($v) => $v!=='');
        $pvData = array_filter($request->input('presencia_varroa', []), fn($v) => $v!=='');
        $pnData = array_filter($request->input('presencia_nosemosis', []), fn($v) => $v!=='');
        $icData = array_filter($request->input('indice_cosecha', []), fn($v) => $v!=='');
        $piData = array_filter($request->input('preparacion_invernada', []), fn($v) => $v!=='');
        
        // 3) Preparar reemplazos de calidad_reina
        $reemplazos = $request->input('calidad_reina.reemplazos_realizados', []);
        $payloadReemplazos = $reemplazos ? json_encode($reemplazos, JSON_UNESCAPED_UNICODE) : null;
        if ($payloadReemplazos) {
            $crData['reemplazos_realizados'] = $payloadReemplazos;
        }

        // 4) Crear único EstadoNutricional (PCC3)
        $enId = null;
        if (!empty($enData)) {
            $enId = EstadoNutricional::create([
                'objetivo'           => $enData['objetivo'],
                'tipo_alimentacion'  => $enData['tipo_alimentacion']  ?? null,
                'fecha_aplicacion'   => $enData['fecha_aplicacion']   ?? null,
                'insumo_utilizado'   => $enData['insumo_utilizado']   ?? null,
                'dosifiacion'        => $enData['dosifiacion']        ?? null,
                'metodo_utilizado'   => $enData['metodo_utilizado']   ?? null,
            ])->id;
        }

        // 5) Todo en una transacción
        DB::transaction(function() use(
            $apiario,
            $dcData, $crData, $enData, $pvData, $pnData, $icData, $piData,
            $enId
        ) {
            if (! $enId) {
                // Si no hay PCC3, no hacemos nada
                return;
            }

            foreach ($apiario->colmenas as $colmena) {
                // Crear sub‐modelos sólo si tienen datos
                $dcId = $this->tieneDatosRelevantes($dcData, (new DesarrolloCria)->getFillable())
                    ? DesarrolloCria::create($dcData)->id
                    : null;

                $crId = $this->tieneDatosRelevantes($crData, (new CalidadReina)->getFillable())
                    ? CalidadReina::create($crData)->id
                    : null;

                $pvId = $this->tieneDatosRelevantes($pvData, (new PresenciaVarroa)->getFillable())
                    ? PresenciaVarroa::create($pvData)->id
                    : null;

                $pnId = $this->tieneDatosRelevantes($pnData, (new PresenciaNosemosis)->getFillable())
                    ? PresenciaNosemosis::create($pnData)->id
                    : null;

                $icId = $this->tieneDatosRelevantes($icData, (new IndiceCosecha)->getFillable())
                    ? IndiceCosecha::create($icData)->id
                    : null;

                $piId = $this->tieneDatosRelevantes($piData, (new PreparacionInvernada)->getFillable())
                    ? PreparacionInvernada::create($piData)->id
                    : null;

                // Finalmente creamos el SistemaExperto por colmena
                SistemaExperto::create([
                    'apiario_id'              => $apiario->id,
                    'colmena_id'              => $colmena->id,
                    'fecha'                   => now(),
                    'desarrollo_cria_id'      => $dcId,
                    'calidad_reina_id'        => $crId,
                    'estado_nutricional_id'   => $enId,
                    'presencia_varroa_id'     => $pvId,
                    'presencia_nosemosis_id'  => $pnId,
                    'indice_cosecha_id'       => $icId,
                    'preparacion_invernada_id'=> $piId,
                ]);
            }
        });

        return redirect()
            ->route('sistemaexperto.index')
            ->with('success', 'Evaluaciones guardadas correctamente.');
    }

    public function editPcc(Colmena $colmena)
    {
        $apiario = $colmena->apiario;
        $sistemaexperto = SistemaExperto::where('colmena_id', $colmena->id)
            ->with([
                'desarrolloCria',
                'calidadReina',
                'estadoNutricional',
                'presenciaVarroa',
                'presenciaNosemosis',
                'indiceCosecha',
                'preparacionInvernada'
            ])
            ->latest('fecha')
            ->first();

        // Armamos el array $valores con toArray() de cada relación
        $valores = [
            'desarrollo_cria'      => optional($sistemaexperto->desarrolloCria)->toArray()      ?? [],
            'calidad_reina'        => optional($sistemaexperto->calidadReina)->toArray()        ?? [],
            'estado_nutricional'   => optional($sistemaexperto->estadoNutricional)->toArray()   ?? [],
            'presencia_varroa'     => optional($sistemaexperto->presenciaVarroa)->toArray()     ?? [],
            'presencia_nosemosis'  => optional($sistemaexperto->presenciaNosemosis)->toArray()  ?? [],
            'indice_cosecha'       => optional($sistemaexperto->indiceCosecha)->toArray()       ?? [],
            'preparacion_invernada'=> optional($sistemaexperto->preparacionInvernada)->toArray()?? [],
        ];

        return view('sistemaexperto.create', compact('apiario', 'colmena', 'sistemaexperto','valores'));
    }


    public function update(Request $request, SistemaExperto $sistemaexperto)
    {
        // Verificación de seguridad para evitar actualizaciones erróneas
        if (!$sistemaexperto->colmena) {
            abort(403, 'Este registro no está vinculado a una colmena.');
        }
        $request->validate([
            'desarrollo_cria.*'               => 'nullable',
            'calidad_reina.*'                 => 'nullable',
            'estado_nutricional.*'            => 'nullable',
            'estado_nutricional.objetivo'     => 'nullable|in:estimulacion,mantencion',
            'presencia_varroa.*'              => 'nullable',
            'presencia_nosemosis.*'           => 'nullable',
            'indice_cosecha.*'                => 'nullable',
            'preparacion_invernada.*'         => 'nullable',
        ]);

        DB::transaction(function () use ($request, $sistemaexperto) {
            $clean = fn(array $a): array => collect($a)
                ->map(fn($v) => $v === '' ? null : $v)
                ->reject(fn($v) => is_null($v))
                ->toArray();

            // DesarrolloCria
            $dc = $clean($request->input('desarrollo_cria', []));
            if ($this->tieneDatosRelevantes($dc, (new DesarrolloCria)->getFillable())) {
                if ($sistemaexperto->desarrollo_cria_id) {
                    $sistemaexperto->desarrolloCria->update($dc);
                } else {
                    $sistemaexperto->desarrollo_cria_id = DesarrolloCria::create($dc)->id;
                }
            }

            // CalidadReina
            $cr = $clean($request->input('calidad_reina', []));
            if ($this->tieneDatosRelevantes($cr, (new CalidadReina)->getFillable())) {
                if ($sistemaexperto->calidad_reina_id) {
                    $sistemaexperto->calidadReina->update($cr);
                } else {
                    $sistemaexperto->calidad_reina_id = CalidadReina::create($cr)->id;
                }
            }

            // EstadoNutricional
            $en = $clean($request->input('estado_nutricional', []));
            if ($this->tieneDatosRelevantes($en, (new EstadoNutricional)->getFillable())) {
                if ($sistemaexperto->estado_nutricional_id) {
                    $sistemaexperto->estadoNutricional->update($en);
                } else {
                    $sistemaexperto->estado_nutricional_id = EstadoNutricional::create($en)->id;
                }
            }

            // PresenciaVarroa
            $pv = $clean($request->input('presencia_varroa', []));
            if ($this->tieneDatosRelevantes($pv, (new PresenciaVarroa)->getFillable())) {
                if ($sistemaexperto->presencia_varroa_id) {
                    $sistemaexperto->presenciaVarroa->update($pv);
                } else {
                    $sistemaexperto->presencia_varroa_id = PresenciaVarroa::create($pv)->id;
                }
            }

            // PresenciaNosemosis
            $pn = $clean($request->input('presencia_nosemosis', []));
            if ($this->tieneDatosRelevantes($pn, (new PresenciaNosemosis)->getFillable())) {
                if ($sistemaexperto->presencia_nosemosis_id) {
                    $sistemaexperto->presenciaNosemosis->update($pn);
                } else {
                    $sistemaexperto->presencia_nosemosis_id = PresenciaNosemosis::create($pn)->id;
                }
            }

            // IndiceCosecha
            $ic = $clean($request->input('indice_cosecha', []));
            if ($this->tieneDatosRelevantes($ic, (new IndiceCosecha)->getFillable())) {
                if ($sistemaexperto->indice_cosecha_id) {
                    $sistemaexperto->indiceCosecha->update($ic);
                } else {
                    $sistemaexperto->indice_cosecha_id = IndiceCosecha::create($ic)->id;
                }
            }

            // PreparacionInvernada
            $pi = $clean($request->input('preparacion_invernada', []));
            if ($this->tieneDatosRelevantes($pi, (new PreparacionInvernada)->getFillable())) {
                if ($sistemaexperto->preparacion_invernada_id) {
                    $sistemaexperto->preparacionInvernada->update($pi);
                } else {
                    $sistemaexperto->preparacion_invernada_id = PreparacionInvernada::create($pi)->id;
                }
            }

            $sistemaexperto->save();
        });

        return redirect()
            ->route('sistemaexperto.index')
            ->with('success', 'Evaluación actualizada correctamente.');
    }

    
    public function destroy(SistemaExperto $sistemaexperto)
    {
        $sistemaexperto->delete();
        return back()->with('success','Registro eliminado.');
    }

    // Listar apiarios para sistema experto
    public function indexSistemaExperto()
    {
        $user = Auth::user();
        $apiarios = Apiario::where('user_id', $user->id)->get();
        return view('sistemaexperto.index', compact('apiarios'));
    }

    
}
