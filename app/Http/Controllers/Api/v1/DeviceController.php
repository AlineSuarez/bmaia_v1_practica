<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function register(Request $r)
    {
        $data = $r->validate([
            'fcm_token' => 'required|string',
            'device_id' => 'required|string',
            'platform'  => 'required|string|in:android,ios,web',
        ]);
        // TODO: persistir en BD
        return response()->json(['status'=>'registered'], 201);
    }
    public function destroy(Request $r, string $device_id)
    {
        // TODO: borrar de BD
        return response()->json(['status'=>'deleted']);
    }
}
