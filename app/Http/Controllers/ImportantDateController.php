<?php

namespace App\Http\Controllers;

use App\Models\ImportantDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportantDateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Devuelve todas las fechas importantes del usuario.
     */
    public function index()
    {
        $dates = ImportantDate::where('user_id', Auth::id())
                               ->orderBy('date','asc')
                               ->get();

        return response()->json($dates);
    }

    /**
     * Crea una nueva fecha importante.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'date'            => 'required|date',
            'recurs_annually' => 'boolean',
            'notes'           => 'nullable|string',
        ]);
        $data['user_id'] = Auth::id();
        //$date = ImportantDate::create($data);
        $date = Auth::user()->importantDates()->create($data);
        return response()->json($date, 201);
    }

    /**
     * Actualiza una fecha importante existente.
     */
    public function update(Request $request, ImportantDate $date)
    {
        $this->authorize('update', $date);

        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'date'            => 'required|date',
            'recurs_annually' => 'boolean',
            'notes'           => 'nullable|string',
        ]);

        $date->update($data);

        return response()->json($date);
    }

    /**
     * Elimina una fecha importante.
     */
    public function destroy(ImportantDate $date)
    {
        $this->authorize('delete', $date);

        $date->delete();

        return response()->json(null, 204);
    }
}