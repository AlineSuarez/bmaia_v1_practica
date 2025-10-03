<?php

namespace App\Http\Requests\Visitas;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitaInspeccionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'visita_id'                  => ['required','integer','exists:visitas,id'],
            'num_colmenas_totales'       => ['nullable','integer','min:0'],
            'num_colmenas_inspeccionadas'=> ['nullable','integer','min:0'],
            'num_colmenas_enfermas'      => ['nullable','integer','min:0'],
            'num_colmenas_activas'       => ['nullable','integer','min:0'],
            'num_colmenas_muertas'       => ['nullable','integer','min:0'],
            'flujo_nectar_polen'         => ['nullable','string','max:255'],
            'nombre_revisor_apiario'     => ['nullable','string','max:255'],
            'sospecha_enfermedad'        => ['nullable','boolean'],
            'observaciones'              => ['nullable','string'],
        ];
    }
}
