<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApiarioRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        // Aceptar 'tipo' como alias y normalizar números
        $this->merge([
            'tipo_apiario'        => $this->input('tipo_apiario', $this->input('tipo')),
            'colmenas_iniciales'  => $this->filled('colmenas_iniciales') ? (int) $this->input('colmenas_iniciales') : null,
            'region_id'           => $this->filled('region_id') ? (int) $this->input('region_id') : null,
            'comuna_id'           => $this->filled('comuna_id') ? (int) $this->input('comuna_id') : null,
        ]);
    }

    public function rules(): array
    {
        $maxNombre = 255 * 2;
        return [
            'nombre'               => 'required|string|max:' . $maxNombre,
            'tipo_apiario' => 'required|in:fijo,trashumante,temporal',
            'region' => 'nullable|string|max:120',
            'comuna' => 'nullable|string|max:120',
            'region_id' => 'nullable|integer|exists:regiones,id',
            'comuna_id' => 'nullable|integer|exists:comunas,id',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'direccion' => 'nullable|string|max:255',
            'colmenas_iniciales' => 'nullable|integer|min:0|max:5000',
            'activo' => 'sometimes|boolean',
            'temporada_produccion' => 'nullable|integer|min:2000|max:' . now()->year,
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_apiario.in' => 'El tipo de apiario debe ser: fijo, trashumante o temporal.',
        ];
    }
}
