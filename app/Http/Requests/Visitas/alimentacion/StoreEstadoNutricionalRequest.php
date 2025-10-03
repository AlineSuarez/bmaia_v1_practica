<?php

namespace App\Http\Requests\Visitas;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstadoNutricionalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'visita_id'            => ['required','integer','exists:visitas,id'],
            'reserva_miel_polen'   => ['nullable','string','max:255'],
            'tipo_alimentacion'    => ['nullable','string','max:255'],
            'alimentacion_azucar'  => ['nullable','boolean'],
            'alimentacion_proteica'=> ['nullable','boolean'],
            'observaciones'        => ['nullable','string'],
        ];
    }
}
