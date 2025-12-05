<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apiario;
use App\Models\User;
use Illuminate\Http\Request;

class AdminApiariosController extends Controller
{
    public function index(Request $request)
    {
        $query = Apiario::with(['user', 'comuna.region'])->withCount('colmenas');

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('localizacion', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('comuna', function($comunaQuery) use ($search) {
                      $comunaQuery->where('nombre', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('comuna.region', function($regionQuery) use ($search) {
                      $regionQuery->where('nombre', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filtro por tipo de apiario
        if ($request->has('tipo') && $request->tipo != '') {
            $query->where('tipo_apiario', $request->tipo);
        }

        // Filtro por estado (activo/inactivo)
        if ($request->has('estado') && $request->estado != '') {
            $query->where('activo', $request->estado);
        }

        // Filtro por tipo 2 (temporales/base/archivados)
        if ($request->has('tipo2') && $request->tipo2 != '') {
            if ($request->tipo2 == 'temporal') {
                $query->where('es_temporal', 1);
            } elseif ($request->tipo2 == 'base') {
                $query->where('es_temporal', 0);
            } elseif ($request->tipo2 == 'archivados') {
                $query->where('activo', 0);
            }
        }

        $apiarios = $query->orderBy('created_at', 'desc')->paginate(15);

        // Conteos para los botones
        $totalTemporales = Apiario::where('es_temporal', 1)->count();
        $totalBase = Apiario::where('es_temporal', 0)->count();
        // Contar apiarios archivados (activo = 0) en lugar de los de papelera
        $totalArchivados = Apiario::where('activo', 0)->count();

        return view('admin.apiarios.index', compact('apiarios', 'totalTemporales', 'totalBase', 'totalArchivados'));
    }

    public function show($id)
    {
        $apiario = Apiario::with(['user', 'comuna.region', 'colmenas', 'visitas' => function($query) {
            $query->orderBy('fecha_visita', 'desc')->limit(10);
        }])->withCount(['colmenas', 'visitas'])->findOrFail($id);

        return view('admin.apiarios.show', compact('apiario'));
    }

    public function destroy($id)
    {
        $apiario = Apiario::findOrFail($id);

        // Verificar si tiene colmenas asociadas
        if ($apiario->colmenas()->count() > 0) {
            return redirect()->route('admin.apiarios.index')
                ->with('error', 'No se puede eliminar el apiario porque tiene colmenas asociadas.');
        }

        $apiario->delete();

        return redirect()->route('admin.apiarios.index')
            ->with('success', 'Apiario eliminado correctamente.');
    }

    /**
     * Mostrar apiarios eliminados (papelera)
     */
    public function deleted()
    {
        $deletedService = new \App\Services\DeletedApiarioService();
        $deletedApiarios = $deletedService->getAllDeleted();

        return view('admin.apiarios.deleted', compact('deletedApiarios'));
    }

    /**
     * Restaurar apiario desde papelera
     */
    public function restore($apiarioId)
    {
        $deletedService = new \App\Services\DeletedApiarioService();
        $apiario = $deletedService->restoreApiario($apiarioId);

        if (!$apiario) {
            return redirect()->route('admin.apiarios.deleted')
                ->with('error', 'El apiario no se encontró o ya expiró.');
        }

        return redirect()->route('admin.apiarios.deleted')
            ->with('success', "Apiario '{$apiario->nombre}' restaurado correctamente con {$apiario->colmenas()->count()} colmenas.");
    }

    /**
     * Eliminar permanentemente apiario de la papelera
     */
    public function permanentDelete($apiarioId)
    {
        $deletedService = new \App\Services\DeletedApiarioService();
        $data = $deletedService->getDeletedApiario($apiarioId);

        if (!$data) {
            return redirect()->route('admin.apiarios.deleted')
                ->with('error', 'El apiario no se encontró o ya expiró.');
        }

        $deletedService->permanentlyDelete($apiarioId);

        return redirect()->route('admin.apiarios.deleted')
            ->with('success', "Apiario '{$data['nombre']}' eliminado permanentemente.");
    }
}
