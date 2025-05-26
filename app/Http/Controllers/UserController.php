<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\Models\User;
use App\Models\Region;
use App\Models\Comuna;

class UserController extends Controller
{
    // Actualizar nombre del usuario
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return redirect()->back()->with('success', 'Nombre actualizado correctamente.');
    }

    public function settings()
    {
        $user = Auth::user();
        $regiones = Region::with('comunas')->get();
        return view('user.settings',compact('user', 'regiones'));
    }

    public function updateProfile()
    {
        $data = $request->validate([
            'rut'           => ['required','regex:/^\d{1,2}\.\d{3}\.\d{3}-[0-9Kk]{1}$/'],
            'razon_social'  => ['nullable','string','max:100'],
            'name'          => ['required','string','max:50'],
            'last_name'     => ['nullable','string','max:50'],
            'phone'         => ['required','digits:9'],
            'email'         => ['required','email','max:100','unique:users,email,'.auth()->id()],
            'id_region'     => ['nullable','exists:regions,id'],
            'id_comuna'     => ['nullable','exists:comunas,id'],
            'address'       => ['nullable','string','max:150'],
            'nregistro'     => ['nullable','string','max:50'],
            'profile_picture' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ]);
        $user = auth()->user();

        // Si vienen datos de archivo, guarda avatar
        if ($request->hasFile('profile_picture')) {
            // elimina anterior si existe
            if ($user->profile_picture) {
                \Storage::disk('public')->delete($user->profile_picture);
            }
            // almacena el nuevo
            $path = $request->file('profile_picture')->store('avatars','public');
            $user->profile_picture = $path;
        }

        // ahora actualiza los demás campos
        $user->rut             = $data['rut'];
        $user->razon_social    = $data['razon_social'];
        $user->name            = $data['name'];
        $user->last_name       = $data['last_name'];
        $user->telefono        = $data['phone'];
        $user->email           = $data['email'];
        $user->id_region       = $data['id_region'];
        $user->id_comuna       = $data['id_comuna'];
        $user->direccion       = $data['address'];
        $user->numero_registro = $data['nregistro'];
        
        $user->save();
        return back()->with('success_settings','Datos de perfil actualizados.');
    }

    // Actualizar avatar del usuario
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'profile_picture' => ['required','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        $user = Auth::user();

        // Eliminar avatar anterior si existe
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Guardar el archivo nuevo
        $path = $request->file('profile_picture')->store('avatars','public');
        $user->update(['profile_picture' => $path]);

        return back()->with('success', 'Avatar actualizado correctamente.');
    }

    public function updateInvoiceSettings(Request $request)
    {
        $data = $request->validate([
            'invoice_company_name'  => 'nullable|string|max:100',
            'invoice_rut'           => ['nullable','regex:/^\d{1,2}\.\d{3}\.\d{3}-[0-9Kk]{1}$/'],
            'invoice_activity'      => 'nullable|string|max:100',
            'invoice_address'       => 'nullable|string|max:150',
            'invoice_region'        => 'nullable|exists:regions,id',
            'invoice_comuna'        => 'nullable|exists:comunas,id',
            'invoice_city'          => 'nullable|string|max:50',
            'invoice_phone'         => 'nullable|digits:9',
            'invoice_email'         => 'nullable|email|max:100',
            'invoice_email_opt_in'  => 'nullable|boolean',
            'invoice_email_dte'     => 'nullable|email|max:100',
        ]);

        Auth::user()->update($data);

        if ($request->wantsJson()) {
            return response()->json(['message'=>'Invoice settings guardados']);
        }
        return back()->with('success_settings','Invoice settings actualizados');
    }

    // Restablecer contraseña del usuario
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();
        return redirect()->back()->with('success_password', 'Contraseña actualizada correctamente.');
    }

    // Datos de facturacion
    public function updateBilling(Request $request)
    {
        $data = $request->validate([
            'billing_razon_social'    => ['nullable','string','max:100'],
            'billing_rut'             => ['nullable','regex:/^\d{1,2}\.\d{3}\.\d{3}-[0-9Kk]{1}$/'],
            'billing_giro'            => ['nullable','string','max:100'],
            'billing_direccion'       => ['nullable','string','max:150'],
            'billing_region'          => ['nullable','exists:regions,id'],
            'billing_comuna'          => ['nullable','exists:comunas,id'],
            'billing_ciudad'          => ['nullable','string','max:50'],
            'billing_telefono'        => ['nullable','digits:9'],
            'billing_email'           => ['nullable','email','max:100'],
            'billing_authorize_email' => ['sometimes','accepted'],
            'billing_email_dte'       => ['nullable','email','max:100'],
        ]);
        auth()->user()->update($data);
        return back()->with('success_settings','Datos de facturación actualizados.');
    }

    // Actualizar permisos
    public function updatePermissions(Request $request)
    {
        $data = $request->validate([
            'allow_notifications' => ['nullable','boolean'],
            'allow_camera'        => ['nullable','boolean'],
            'allow_microphone'    => ['nullable','boolean'],
            'allow_location'      => ['nullable','boolean'],
            'allow_bluetooth'     => ['nullable','boolean'],
        ]);
        $user = auth()->user();
        // Marcamos cada permiso según si el checkbox estuvo presente en el form
        $user->allow_notifications = $request->has('allow_notifications');
        $user->allow_camera        = $request->has('allow_camera');
        $user->allow_microphone    = $request->has('allow_microphone');
        $user->allow_location      = $request->has('allow_location');
        $user->allow_bluetooth     = $request->has('allow_bluetooth');
        $user->save();
        
        return redirect()->back()->with('success_settings','Permisos actualizados correctamente.');
    }

    public function updateUtilities(Request $request)
    {
        // Validamos primero qué tipo de utilidad estamos creando
        $payload = $request->validate([
            'type' => 'required|in:alert,reminder,important_date,emergency_contact',
        ]);
        $user = auth()->user();
        switch ($payload['type']) {
            case 'alert':
                $data = $request->validate([
                    'title'       => 'required|string|max:100',
                    'description' => 'nullable|string',
                    'type_alert'  => 'required|in:inspection,feeding,harvest,treatment,other',
                    'date'        => 'required|date',
                    'priority'    => 'required|in:low,medium,high',
                ]);
                $created = $user->alerts()->create([
                    'title'       => $data['title'],
                    'description' => $data['description'],
                    'type'        => $data['type_alert'],
                    'date'        => $data['date'],
                    'priority'    => $data['priority'],
                ]);
                break;
            case 'reminder':
                $data = $request->validate([
                    'title'  => 'required|string|max:100',
                    'date'   => 'required|date',
                    'time'   => 'nullable|date_format:H:i',
                    'repeat' => 'required|in:none,daily,weekly,monthly',
                    'notes'  => 'nullable|string',
                ]);
                $created = $user->reminders()->create($data);
                break;
            case 'important_date':
                $data = $request->validate([
                    'title'     => 'required|string|max:100',
                    'type_date' => 'required|in:birthday,anniversary,flowering,event,other',
                    'value'     => 'required|date',
                    'recurring' => 'nullable|boolean',
                    'notes'     => 'nullable|string',
                ]);
                $created = $user->importantDates()->create([
                    'title'     => $data['title'],
                    'type'      => $data['type_date'],
                    'value'     => $data['value'],
                    'recurring' => $request->has('recurring'),
                    'notes'     => $data['notes'],
                ]);
                break;
            case 'emergency_contact':
                $data = $request->validate([
                    'name'     => 'required|string|max:100',
                    'relation' => 'required|in:family,friend,colleague,vet,emergency,supplier,other',
                    'phone'    => 'required|regex:/^\d{9}$/',
                    'email'    => 'nullable|email',
                    'address'  => 'nullable|string',
                    'notes'    => 'nullable|string',
                ]);
                $created = $user->emergencyContacts()->create($data);
                break;
        }
        return response()->json($created, 201);
    }

    // Seleccion de plan
    public function updatePlan(Request $request)
    {
        $data = $request->validate([
            'plan' => ['required','in:drone,afc,me,ge,queen'],
        ]);
        $user = auth()->user();
        $user->plan = $data['plan'];
        $user->plan_start_date = now();
        $user->plan_end_date   = now()->addYear();
        $user->save();
        return redirect()->route('user.settings')->with('success_settings','Plan actualizado a “'.strtoupper($data['plan']).'”.');
    }

    public function updateSettings(Request $request)
    {
        // Validar la solicitud
        $validated = $request->validate([
            'rut' => ['required', 'regex:/^\d{1,2}\.\d{3}\.\d{3}-[0-9Kk]{1}$/'],
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'razon_social' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^\d{9}$/'],
            'id_region' => ['nullable', 'exists:regiones,id'],
            'id_comuna' => ['nullable', 'exists:comunas,id'],
            'nregistro' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . Auth::id()],
        ]);
    
        // Buscar al usuario por su ID
        $user = Auth::user();
    
        // Completar o actualizar los datos del usuario
        $user->rut = $validated['rut'] ?? $user->rut;
        $user->name = $validated['name'] ?? $user->name;
        $user->last_name = $validated['last_name'] ?? $user->last_name;
        $user->telefono = $validated['phone'] ?? $user->telefono;
        $user->razon_social = trim($request->razon_social) !== '' ? $request->razon_social : null;
        $user->id_region = $validated['id_region'] ?? $user->id_region;
        $user->id_comuna = $validated['id_comuna'] ?? $user->id_comuna;
        $user->numero_registro = $validated['nregistro'] ?? $user->numero_registro;
        $user->direccion = $validated['address'] ?? $user->direccion;
        $user->email = $validated['email'] ?? $user->email;
    
        // Guardar los cambios
        $user->save();
    
        return redirect()->back()->with('success_settings', 'Información actualizada correctamente.');
    }
    

}
