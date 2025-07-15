<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatoFacturacion;


class DatoFacturacionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'rut' => 'required|string|max:255',
            'giro' => 'nullable|string|max:255',
            'direccion_comercial' => 'nullable|string|max:255',
            'billing_region' => 'nullable|integer|exists:regiones,id',
            'billing_comuna' => 'nullable|integer|exists:comunas,id',
            'ciudad' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255',
            'autorizacion_envio_dte' => 'nullable|boolean',
            'correo_envio_dte' => 'nullable|email|max:255',
        ]);

        // Buscar o crear registro para el usuario
        $datos = DatoFacturacion::firstOrNew(['user_id' => auth()->id()]);

        // Asignar campos individualmente
        $datos->update([
            'razon_social' => $request->razon_social,
            'rut' => $request->rut,
            'giro' => $request->giro,
            'direccion_comercial' => $request->direccion_comercial,
            'region_id' => $request->billing_region,
            'comuna_id' => $request->billing_comuna,
            'ciudad' => $request->ciudad,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'correo_envio_dte' => $request->correo_envio_dte,
            'autorizacion_envio_dte' => $request->has('autorizacion_envio_dte'),
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Datos de facturación guardados correctamente.');
    }
}
