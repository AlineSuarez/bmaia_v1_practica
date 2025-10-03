<?php

namespace App\Http\Requests\Visitas;

use Illuminate\Foundation\Http\FormRequest;

class StorePresenciaVarroaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'visita_id'           => ['required','integer','exists:visitas,id'],
            'diagnostico_visual'  => ['nullable','string','max:255'],
            'porcentaje_infestacion' => ['nullable','numeric','min:0'],
            'tratamiento'         => ['nullable','string','max:255'], // “medicamento”
            'observaciones'       => ['nullable','string'],
        ];
    }
}
