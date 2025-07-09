<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Apiario;
use App\Models\Comuna;
use App\Models\Region;
use Auth;


class ApiarioController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        // 1) Fijos
        $apiariosFijos = Apiario::with('comuna')
            ->where('user_id', $userId)
            ->where('tipo_apiario', 'fijo')
            ->get();

        // 2) Trashumantes “base” (NO temporales)
        $apiariosBase = Apiario::where('user_id', $userId)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->where('es_temporal', false)    // ← filtrar aquí
            ->get();

        // 3) Apiarios temporales (los que creaste con el wizard)
        $apiariosTemporales = Apiario::where('user_id', $userId)
            ->where('tipo_apiario', 'trashumante')
            ->where('activo', 1)
            ->where('es_temporal', true)  // ← y aquí
            ->get();

        // 4) Apiarios Archivados → aquellos que ya no estén activos (activo = 0)
        $apiariosArchivados = Apiario::where('user_id', $userId)
            ->where('tipo_apiario', 'trashumante')
            ->where('es_temporal', true)
            ->where('activo', 0)
            ->with('comuna.region', 'ultimoMovimientoDestino.apiarioOrigen.comuna.region')
            ->orderByDesc('updated_at')
            ->get();

        return view('apiarios.index', compact(
            'apiariosFijos',
            'apiariosBase',
            'apiariosTemporales',
            'apiariosArchivados'
        ));
    }

    // Mostrar formulario para crear un nuevo apiario
    public function create()
    { // Obtener todas las regiones
        $regiones = Region::with('comunas')->get();

        //Recuperar las comunas de la región seleccionada
        $comunas = Comuna::all();

        // Preparar coordenadas de las comunas para usar en el mapa
        $comunasCoordenadas = $comunas->mapWithKeys(function ($comuna) {
            return [$comuna->nombre => ['lat' => $comuna->lat, 'lon' => $comuna->lon]];
        })->toArray();

        return view('apiarios.create', compact('regiones', 'comunasCoordenadas'));
    }

    // Guardar un nuevo apiario
    public function store(Request $request)
    {
        // Validación corregida y unificada
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'temporada_produccion' => 'required|string|max:255',
            'registro_sag' => 'required|string|max:255',
            'num_colmenas' => 'required|integer',
            'tipo_apiario' => 'required|in:fijo,trashumante',
            'tipo_manejo' => 'required|string|max:255',
            'objetivo_produccion' => 'required|string|max:255',
            'region' => 'required|exists:regiones,id',
            'comuna' => 'required|exists:comunas,id',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'localizacion' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,bmp,svg|max:10240',
        ]);

        // 2) completamos campos extra
        $data['user_id'] = auth()->id();
        $data['region_id'] = $data['region'];
        $data['comuna_id'] = $data['comuna'];
        unset($data['region'], $data['comuna']);

        $data['activo'] = 1;
        $data['es_temporal'] = false;

        // Manejo mejorado de la foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');

            // Generar nombre único para evitar conflictos
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Guardar en storage/app/public/fotos_apiarios
            $data['foto'] = $file->storeAs('fotos_apiarios', $filename, 'public');
        }

        // 4) creamos el modelo de una
        $apiario = Apiario::create($data);
        for ($i = 1; $i <= $apiario->num_colmenas; $i++) {
            //$codigo = (string) \Str::uuid();
            $codigo = url("/apiarios/{$apiario->id}/colmenas/{$i}"); // URL real al detalle de la colmena
            $apiario->colmenas()->create([
                'nombre' => 'Colmena ' . $i, // ← Esto es CLAVE
                'numero' => (string) $i,
                'color_etiqueta' => 'Amarillo',
                'codigo_qr' => $codigo,
            ]);
        }
        return redirect()
            ->route('apiarios')
            ->with('success', 'Apiario creado con éxito');
    }


    // Método para retornar comunas en función de la región
    public function getComunas($regionId)
    {
        $comunas = Comuna::where('region_id', $regionId)->get();
        return response()->json($comunas);
    }
    // Método para retornar comunas en función de la región
    public function deleterApiario($apiarioId)
    {
        // Buscar el apiario por ID y verificar que pertenezca al usuario autenticado
        $apiario = Apiario::where('id', $apiarioId)
            ->where('user_id', auth()->id())
            ->first(); // Usa first() para obtener una instancia del modelo
        // Verifica si el apiario existe
        if ($apiario) {
            $apiario->delete(); // Elimina el apiario
            return redirect()->route('apiarios')->with('success', 'Apiario eliminado con éxito');
        } else {
            // Si el apiario no existe, puedes manejar el error aquí
            return redirect()->route('apiarios')->with('error', 'Apiario no encontrado o no autorizado para eliminar.');
        }
    }

    public function massDelete(Request $request)
    {
        // Verifica si la solicitud contiene la lista de IDs
        $ids = $request->input('ids');
        if ($ids && is_array($ids)) {
            // Elimina todos los apiarios con los IDs proporcionados
            Apiario::whereIn('id', $ids)->delete();
        }
        // Puedes regresar una respuesta JSON o redirigir
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $apiario = Apiario::findOrFail($id);
        $regiones = Region::all();
        $comunas = Comuna::all();

        // Preparar coordenadas de comunas para el mapa
        $comunasCoordenadas = $comunas->mapWithKeys(function ($comuna) {
            return [$comuna->nombre => ['lat' => $comuna->lat, 'lon' => $comuna->lon]];
        })->toArray();

        return view('apiarios.edit', compact('apiario', 'regiones', 'comunas', 'comunasCoordenadas'));
    }

    public function update(Request $request, $id)
    {
        // VALIDACIÓN CORREGIDA - ahora acepta todos los formatos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'temporada_produccion' => 'required|string',
            'registro_sag' => 'required|string',
            'num_colmenas' => 'required|integer',
            'tipo_apiario' => 'required|string',
            'tipo_manejo' => 'required|string',
            'objetivo_produccion' => 'required|string',
            'region' => 'required|integer',
            'comuna' => 'required|integer',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            // VALIDACIÓN CORREGIDA - acepta todos los formatos y tamaño mayor
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,bmp,svg|max:10240' // 10MB
        ]);

        $apiario = Apiario::findOrFail($id);

        // Manejo mejorado de la nueva foto
        if ($request->hasFile('foto')) {
            // Eliminar la foto anterior si existe
            if ($apiario->foto && Storage::disk('public')->exists($apiario->foto)) {
                Storage::disk('public')->delete($apiario->foto);
            }

            $file = $request->file('foto');

            // Generar nombre único
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Guardar la nueva foto
            $path = $file->storeAs('fotos_apiarios', $filename, 'public');
            $apiario->foto = $path;
        }

        // Actualizar otros campos del apiario
        $apiario->nombre = $request->nombre;
        $apiario->temporada_produccion = $request->temporada_produccion;
        $apiario->registro_sag = $request->registro_sag;
        $apiario->num_colmenas = $request->num_colmenas;
        $apiario->tipo_apiario = $request->tipo_apiario;
        $apiario->tipo_manejo = $request->tipo_manejo;
        $apiario->objetivo_produccion = $request->objetivo_produccion;
        $apiario->region_id = $request->region;
        $apiario->comuna_id = $request->comuna;
        $apiario->latitud = $request->latitud;
        $apiario->longitud = $request->longitud;

        $apiario->save();

        return redirect()->route('apiarios')->with('success', 'Apiario actualizado exitosamente.');
    }

    // Listar apiarios para sistema experto
    public function indexSistemaExperto()
    {
        $user = Auth::user();
        $apiarios = Apiario::where('user_id', $user->id)->get();
        return view('sistemaexperto.index', compact('apiarios'));
    }

    // Obtener consejo (simulado) basado en registros PCC completos
    public function obtenerConsejo($apiario_id)
    {
        $apiario = Apiario::findOrFail($apiario_id);

        /*
        // Solo visitas de tipo "Sistema Experto"
        $ultimaVisita = $apiario->visitas()
            ->where('tipo_visita', 'Sistema Experto')
            ->latest()
            ->first();

        $requisitosPCC = [
            'desarrollo_cria_id',
            'calidad_reina_id',
            'estado_nutricional_id',
            'presencia_varroa_id',
            'presencia_nosemosis_id',
            'indice_cosecha_id',
            'preparacion_invernada_id'
        ];

        $faltantes = collect($requisitosPCC)->filter(fn($campo) => !$ultimaVisita || !$ultimaVisita->$campo);
        $puede_registrar_pcc = ($faltantes->count() > 0);

        if ($puede_registrar_pcc) {
            return response()->json([
                'success' => false,
                'message' => 'Debes registrar un PCC completo antes de generar el consejo.',
                'registrar_pcc_url' => route('sistemaexperto.create', $apiario->id),
                'puede_registrar_pcc' => true,
            ]);
        }
         */

        // Consejo simulado aleatorio
        $consejos = [
            "Monitorea varroa y fortalece la alimentación proteica antes del invierno.",
            "El apiario muestra buen desarrollo, revisa reservas y la postura de la reina.",
            "Sugiero rotar cuadros y aplicar tratamiento preventivo para Nosema.",
            "Estado general adecuado. Actualiza registros periódicamente.",
            "Detectada baja actividad, revisa la calidad de la reina y alimentación.",
            "Apiario saludable, recuerda monitorear enfermedades y reservas de miel."
        ];
        $consejo = $consejos[array_rand($consejos)];

        return response()->json([
            'success' => true,
            'consejo' => $consejo,
            'apiario' => $apiario->id,
        ]);
    }

    public function createTemporal(Request $request)
    {
        $tipo = $request->get('tipo'); // 'traslado' o 'retorno'
        $apiarios = $request->get('apiarios'); // IDs separados por coma

        if (!$apiarios) {
            return redirect()->route('apiarios.index')->with('error', 'No se seleccionaron apiarios.');
        }

        $apiarioIds = explode(',', $apiarios);

        // Obtener los datos de los apiarios seleccionados
        $apiariosData = Apiario::whereIn('id', $apiarioIds)->get();
        $regiones     = Region::all();       
        if ($apiariosData->isEmpty()) {
            return redirect()->route('apiarios.index')->with('error', 'No se encontraron los apiarios seleccionados.');
        }

        return view('apiarios.create-temporal', compact('tipo', 'apiariosData', 'regiones'));
    }

    // Método show que falta
    public function show($id)
    {
        $apiario = Apiario::findOrFail($id);

        // Verificar que el apiario pertenece al usuario autenticado
        if ($apiario->user_id !== auth()->id()) {
            return redirect()->route('apiarios.index')->with('error', 'No tienes permisos para ver este apiario.');
        }

        return view('apiarios.show', compact('apiario'));
    }

    // Método destroy que también falta (diferente al deleterApiario)
    public function destroy($id)
    {
        $apiario = Apiario::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Eliminar la foto si existe
        if ($apiario->foto) {
            Storage::disk('public')->delete($apiario->foto);
        }

        $apiario->delete();

        return redirect()->route('apiarios.index')->with('success', 'Apiario eliminado con éxito');
    }
}
