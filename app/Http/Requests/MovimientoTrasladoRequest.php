<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovimientoTrasladoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'apiario_origen_id' => 'required|exists:apiarios,id',
            'nombre_temporal'   => 'required|string|max:120',
            'region'            => 'required_without:region_id|string|max:120',
            'comuna'            => 'required_without:comuna_id|string|max:120',
            'region_id'         => 'nullable|integer|exists:regiones,id',
            'comuna_id'         => 'nullable|integer|exists:comunas,id',
            'fecha_inicio'      => 'required|date',
            'fecha_termino'     => 'required|date|after_or_equal:fecha_inicio',
            'colmenas'          => 'required|array|min:1',
            'colmenas.*'        => 'integer|exists:colmenas,id',
            'motivo'            => 'nullable|string|max:120',
            'transportista'     => 'nullable|string|max:120',
            'vehiculo'          => 'nullable|string|max:20',
            'observaciones'     => 'nullable|string|max:500',
        ];
    }
}
