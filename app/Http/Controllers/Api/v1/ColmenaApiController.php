<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreColmenaRequest;
use App\Http\Requests\UpdateColmenaRequest;
use App\Models\Apiario;
use App\Models\Colmena;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ColmenaApiController extends Controller
{
    /**
     * GET /api/colmenas
     */
    public function index(Request $request): JsonResponse
    {
        // Puedes filtrar por apiario_id si lo envían: /api/colmenas?apiario_id=123
        $query = Colmena::query();

        if ($request->filled('apiario_id')) {
            $query->where('apiario_id', $request->integer('apiario_id'));
        }

        $colmenas = $query->latest('id')->get();

        return response()->json(['data' => $colmenas]);
    }

    /**
     * POST /api/colmenas
     */
    public function store(StoreColmenaRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Opcional: asegurar que la colmena se crea en un apiario del usuario autenticado
        // (no lo exigen los tests, pero es buena práctica)
        $apiario = Apiario::findOrFail($data['apiario_id']);
        if (auth()->check() && $apiario->user_id !== auth()->id()) {
            abort(403, 'No puedes crear colmenas en un apiario de otro usuario.');
        }

        $colmena = Colmena::create($data);

        return response()->json(['data' => $colmena], 201);
    }

    /**
     * GET /api/colmenas/{colmena}
     */
    public function show(Colmena $colmena): JsonResponse
    {
        return response()->json(['data' => $colmena]);
    }

    /**
     * PUT/PATCH /api/colmenas/{colmena}
     */
    public function update(UpdateColmenaRequest $request, Colmena $colmena): JsonResponse
    {
        // Opcional: ownership check
        if (auth()->check() && $colmena->apiario && $colmena->apiario->user_id !== auth()->id()) {
            abort(403, 'No puedes modificar colmenas de otro usuario.');
        }

        $colmena->fill($request->validated());
        $colmena->save();

        return response()->json(['data' => $colmena]);
    }

    /**
     * DELETE /api/colmenas/{colmena}
     */
    public function destroy(Colmena $colmena): JsonResponse
    {
        // Opcional: ownership check
        if (auth()->check() && $colmena->apiario && $colmena->apiario->user_id !== auth()->id()) {
            abort(403, 'No puedes eliminar colmenas de otro usuario.');
        }

        // Usa SoftDeletes (tu modelo ya tiene SoftDeletes)
        $colmena->delete();

        return response()->json(['ok' => true]);
    }
}
