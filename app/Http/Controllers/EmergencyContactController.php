<?php

namespace App\Http\Controllers;

use App\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listar todos los contactos de emergencia del usuario.
     */
    public function index()
    {
        $contacts = EmergencyContact::where('user_id', Auth::id())
                                    ->orderBy('name')
                                    ->get();

        return response()->json($contacts);
    }

    /**
     * Crear un nuevo contacto de emergencia.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'relation' => 'required|string|max:255',
            'phone'    => 'required|string|max:50',
            'email'    => 'nullable|email|max:255',
            'address'  => 'nullable|string|max:255',
            'notes'    => 'nullable|string',
        ]);
        $data['user_id'] = Auth::id();
        $contact = EmergencyContact::create($data);
        return response()->json($contact, 201);
    }

    /**
     * Actualizar un contacto de emergencia existente.
     */
    public function update(Request $request, EmergencyContact $contact)
    {
        $this->authorize('update', $contact);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'relation' => 'required|string|max:255',
            'phone'    => 'required|string|max:50',
            'email'    => 'nullable|email|max:255',
            'address'  => 'nullable|string|max:255',
            'notes'    => 'nullable|string',
        ]);

        $contact->update($data);

        return response()->json($contact);
    }

    /**
     * Eliminar un contacto de emergencia.
     */
    public function destroy(EmergencyContact $contact)
    {
        $this->authorize('delete', $contact);

        $contact->delete();

        return response()->json(null, 204);
    }
}