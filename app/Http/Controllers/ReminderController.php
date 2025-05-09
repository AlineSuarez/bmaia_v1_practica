<?php
namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReminderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listar todos los recordatorios del usuario.
     */
    public function index()
    {
        $reminders = Reminder::where('user_id', Auth::id())
                            ->orderBy('remind_at','asc')
                            ->get();

        return response()->json($reminders);
    }

    /**
     * Crear un nuevo recordatorio.
     */
    public function store(Request $request)
    {
        // 1) Combinar date + time en remindet_at
        $date = $request->input('date');
        $time = $request->input('time', '00:00');
        $request->merge([
            'remind_at' => Carbon::parse("$date $time")
        ]);

        // 2) Validar el campo correcto
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'notes'     => 'nullable|string',
            'repeat'    => 'nullable|in:none,daily,weekly,monthly',
            'remind_at' => 'required|date',
        ]);

        // 3) Crear vía relación
        $reminder = Auth::user()->reminders()->create($data);

        return response()->json($reminder, 201);
    }

    /**
     * Actualizar un recordatorio existente.
     */
    public function update(Request $request, Reminder $reminder)
    {
        $this->authorize('update', $reminder);

        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'remind_at' => 'required|date',
            'notes'     => 'nullable|string',
        ]);

        $reminder->update($data);

        return response()->json($reminder);
    }

    /**
     * Eliminar un recordatorio.
     */
    public function destroy(Reminder $reminder)
    {
        $this->authorize('delete', $reminder);

        $reminder->delete();

        return response()->json(null, 204);
    }
}
