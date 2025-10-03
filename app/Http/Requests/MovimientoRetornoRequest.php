<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovimientoRetornoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'apiario_temporal_id' => 'required|exists:apiarios,id',
            'apiario_destino_id'  => 'required|exists:apiarios,id',
            'colmenas'            => 'required|array|min:1',
            'colmenas.*'          => 'integer|exists:colmenas,id',
            'fecha_retorno'       => 'required|date',
        ];
    }
}
