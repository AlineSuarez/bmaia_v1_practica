<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreColmenaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Si quieres que todos los usuarios autenticados puedan crear colmenas:
        return true;
    }

    public function rules(): array
    {
        return [
            'apiario_id'     => ['required', 'exists:apiarios,id'],
            'numero'         => ['required', 'string', 'max:50'],
            'codigo_qr'      => ['nullable', 'string', 'max:255'],
            'color_etiqueta' => ['nullable', 'string', 'max:50'],
            'historial'      => ['nullable', 'array'],
            'estado_inicial' => ['nullable', 'string', 'max:100'],
            'numero_marcos'  => ['nullable', 'integer', 'min:0'],
            'observaciones'  => ['nullable', 'string'],
        ];
    }
}
