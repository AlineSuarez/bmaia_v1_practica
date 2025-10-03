<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncRequest extends FormRequest
{
    public function authorize(): bool { return true; } // protegido por Sanctum en la ruta

    public function rules(): array
    {
        return [
            'items' => ['required','array','max:1000'],
            'items.*.uuid'       => ['required','uuid'],
            'items.*.entity'     => ['required','string','in:apiario,colmena,visita'], // agrega las que uses
            'items.*.op'         => ['required','string','in:create,update,delete'],
            'items.*.payload'    => ['required','array'],
            'items.*.updated_at' => ['required','integer'], // epoch ms
            'last_sync_at'       => ['nullable','integer'],
        ];
    }
}
