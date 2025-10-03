<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApiarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tipo_apiario'        => $this->input('tipo_apiario', $this->input('tipo')),
            'region_id'           => $this->filled('region_id') ? (int) $this->input('region_id') : null,
            'comuna_id'           => $this->filled('comuna_id') ? (int) $this->input('comuna_id') : null,
        ]);
    }

    public function rules(): array
    {
        $maxNombre = 255 * 2;
        return [
            'nombre' => 'sometimes|string|max:120',
            'tipo_apiario' => 'sometimes|in:fijo,trashumante,temporal',
            'region' => 'nullable|string|max:120',
            'comuna' => 'nullable|string|max:120',
            'region_id' => 'nullable|integer|exists:regiones,id',
            'comuna_id' => 'nullable|integer|exists:comunas,id',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'direccion' => 'nullable|string|max:255',
            'activo' => 'sometimes|boolean',
        ];
    }
}
