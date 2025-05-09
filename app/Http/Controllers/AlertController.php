<?php


namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::where('user_id', Auth::id())->get();
        return response()->json($alerts);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'type'        => 'required|in:temperature,inspection,treatment,harvest',
                'date'        => 'required|date',
                'priority'    => 'required|in:low,medium,high',
            ]);

            $alert = Alert::create([
                'user_id'     => Auth::id(),
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'type'        => $data['type'],
                'date'        => $data['date'],
                'priority'    => $data['priority'],
            ]);

            return response()->json($alert, 201);
        } catch (\Exception $e) {
            Log::error("Error guardando alerta: ".$e->getMessage());
            // en DEV devolvemos el mensaje, para debugging
            return response()->json([
                'error'   => 'Error interno al crear alerta',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Alert $alert)
    {
        $this->authorize('update', $alert);
        $data = $request->validate([
            'title'       => 'required|string',
            'description' => 'nullable|string',
            'type'        => 'required|in:temperature,inspection,treatment,harvest',
            'date'        => 'required|date',
            'priority'    => 'required|in:low,medium,high',
        ]);
        $alert->update($data);
        return response()->json($alert);
    }

    public function destroy(Alert $alert)
    {
        $this->authorize('delete', $alert);
        $alert->delete();
        return response()->json(null, 204);
    }
}
