<?php

namespace App\Http\Requests\Visitas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEstadoNutricionalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return (new StoreEstadoNutricionalRequest())->rules();
    }
}
