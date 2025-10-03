<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateColmenaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero'         => ['sometimes', 'string', 'max:50'],
            'codigo_qr'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'color_etiqueta' => ['sometimes', 'nullable', 'string', 'max:50'],
            'historial'      => ['sometimes', 'nullable', 'array'],
            'estado_inicial' => ['sometimes', 'nullable', 'string', 'max:100'],
            'numero_marcos'  => ['sometimes', 'nullable', 'integer', 'min:0'],
            'observaciones'  => ['sometimes', 'nullable', 'string'],
        ];
    }
}
