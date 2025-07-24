<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use Exception;

class PermissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            // Esto creará un registro vacío si no existe aún
            $perm = Permission::firstOrCreate(
                ['user_id' => Auth::id()],
                [] // usa valores por defecto de la migración
            );
            return response()->json($perm);
        } catch (Exception $e) {
            // devuelve el mensaje de error al front
            return response()->json([
                'error' => 'Index Exception: '.$e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = $request->validate([
                'notifications'  => 'boolean',
                'camera_access'  => 'boolean',
                'microphone'     => 'boolean',
                'location'       => 'boolean',
                'bluetooth'      => 'boolean',
            ]);
            $perm = Permission::updateOrCreate(
                ['user_id' => Auth::id()],
                $data
            );
            return response()->json([
                'message'     => 'Permisos actualizados',
                'permissions' => $perm
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Update Exception: '.$e->getMessage()
            ], 500);
        }
    }

    public function reset()
    {
        try {
            $perm = Permission::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'notifications' => false,
                    'camera_access' => false,
                    'microphone'    => false,
                    'location'      => false,
                    'bluetooth'     => false,
                ]
            );
            return response()->json([
                'message'     => 'Permisos restablecidos a OFF',
                'permissions' => $perm
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Reset Exception: '.$e->getMessage()
            ], 500);
        }
    }
}
