<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Apiario;
use Illuminate\Http\Request;
use App\Http\Resources\ApiarioResource;
use App\Services\ApiarioService;
use App\Services\GeoService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ApiarioApiController extends Controller
{
    public function __construct(
        private ApiarioService $svc,
        private GeoService $geo
    ) {}

    public function index(Request $request)
    {
        $query = Apiario::where('user_id', auth()->id());

        // Filtros opcionales
        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        if ($request->has('tipo_apiario')) {
            $query->where('tipo_apiario', $request->tipo_apiario);
        }

        if ($request->has('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        // Eager loading
        $query->with(['region', 'comuna']);

        // Paginación
        $apiarios = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $apiarios,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'temporada_produccion' => 'nullable|string|max:100',
            'registro_sag' => 'nullable|string|max:255',
            'num_colmenas' => 'required|integer|min:0',
            'tipo_apiario' => 'required|in:trashumante,temporal',
            'tipo_manejo' => 'required|in:orgánico,convencional',
            'objetivo_produccion' => 'required|in:producción,polinización,cría,mixto',
            'pais' => 'nullable|string|max:255',
            'localizacion' => 'nullable|string|max:255',
            'region_id' => 'required|exists:regiones,id',
            'comuna_id' => 'required|exists:comunas,id',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'foto' => 'nullable|image|max:5120', // 5MB
            'activo' => 'boolean',
            'es_temporal' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['user_id'] = auth()->id();

            // Manejar upload de foto
            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('apiarios', 'public');
            }

            // Crear apiario
            $apiario = Apiario::create($data);

            // Cargar relaciones
            $apiario->load(['region', 'comuna']);

            Log::info('Apiario creado', [
                'user_id' => auth()->id(),
                'apiario_id' => $apiario->id,
                'nombre' => $apiario->nombre,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Apiario creado exitosamente',
                'data' => $apiario,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear apiario', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al crear apiario',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $apiario = Apiario::where('user_id', auth()->id())->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string|max:255',
            'temporada_produccion' => 'nullable|string|max:100',
            'registro_sag' => 'nullable|string|max:255',
            'num_colmenas' => 'sometimes|integer|min:0',
            'tipo_apiario' => 'sometimes|in:trashumante,temporal',
            'tipo_manejo' => 'sometimes|in:orgánico,convencional',
            'objetivo_produccion' => 'sometimes|in:producción,polinización,cría,mixto',
            'region_id' => 'sometimes|exists:regiones,id',
            'comuna_id' => 'sometimes|exists:comunas,id',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'activo' => 'sometimes|boolean',
            'es_temporal' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            $dataAntes = $apiario->toArray();
            
            $apiario->update($validator->validated());
            $apiario->load(['region', 'comuna']);

            // Auditoría
            $this->audit($request, 'apiario.update', $apiario->id, [
                'antes' => $dataAntes,
                'despues' => $apiario->toArray(),
            ]);

            DB::commit();

            Log::info('Apiario actualizado', [
                'user_id' => auth()->id(),
                'apiario_id' => $apiario->id,
            ]);

            Cache::forget('user_apiarios_' . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Apiario actualizado exitosamente',
                'data' => $apiario,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al actualizar apiario', [
                'apiario_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar apiario',
            ], 500);
        }
    }

    public function destroy($id)
    {
        $apiario = Apiario::where('user_id', auth()->id())->findOrFail($id);

        try {
            // Verificar si tiene colmenas activas
            if ($apiario->colmenas()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'No se puede eliminar un apiario con colmenas activas',
                ], 400);
            }

            $apiario->delete();

            Log::info('Apiario eliminado', [
                'user_id' => auth()->id(),
                'apiario_id' => $id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Apiario eliminado exitosamente',
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar apiario', [
                'apiario_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar apiario',
            ], 500);
        }
    }

    public function restore(Request $request, $id) {
        $apiario = Apiario::onlyTrashed()->where('user_id',$request->user()->id)->findOrFail($id);
        $this->authorize('restore', $apiario);
        $apiario->restore();

        $this->audit($request, 'apiario.restore', $apiario->id, $apiario->toArray());
        return response()->json(['ok'=>true, 'data'=>$apiario]);
    }

    private function audit(Request $r, string $action, int $resourceId, array $payload = []) {
        \DB::table('events')->insert([
            'user_id'     => $r->user()->id,
            'action'      => $action,
            'resource'    => 'apiario',
            'resource_id' => $resourceId,
            'payload'     => json_encode($payload),
            'ip'          => $r->ip(),
            'user_agent'  => (string)$r->userAgent(),
            'created_at'  => now(),
        ]);
    }

    
}

