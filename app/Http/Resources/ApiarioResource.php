<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiarioResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'nombre'   => $this->nombre,
            'tipo_apiario'     => $this->tipo,
            'region'   => optional($this->region)->nombre,
            'comuna'   => optional($this->comuna)->nombre,
            'latitud'  => $this->latitud,
            'longitud' => $this->longitud,
            'direccion'=> $this->direccion,
            'activo'   => (bool)$this->activo,
            'colmenas_count' => $this->when(isset($this->colmenas_count), $this->colmenas_count),
            'created_at'=> optional($this->created_at)->toIso8601String(),
            'updated_at'=> optional($this->updated_at)->toIso8601String(),
        ];
    }
}
