<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Todo opcional para updates parciales (los tests solo envían observaciones)
        return [
            'apiario_id' => ['sometimes','integer','exists:apiarios,id'],
            'colmena_id' => ['sometimes','nullable','integer','exists:colmenas,id'],

            'fecha'         => ['sometimes','nullable','date'],
            'fecha_visita'  => ['sometimes','nullable','date'],

            'observaciones' => ['sometimes','nullable','string'],
            'tipo_visita'   => ['sometimes','nullable','string'],
            'vigor_de_colmena'        => ['sometimes','nullable','string'],
            'actividad_colmena'       => ['sometimes','nullable','string'],
            'ingreso_pollen'          => ['sometimes','nullable','string'],
            'bloqueo_camara_cria'     => ['sometimes','nullable','string'],
            'presencia_celdas_reales' => ['sometimes','nullable','string'],
            'postura_de_reina'        => ['sometimes','nullable','string'],
            'estado_de_cria'          => ['sometimes','nullable','string'],
            'postura_zanganos'        => ['sometimes','nullable','string'],
            'reserva_alimento'        => ['sometimes','nullable','string'],
            'presencia_varroa'        => ['sometimes','nullable','string'],

            'visita_general' => ['sometimes','array'],
            'visita_general.motivo'                    => ['sometimes','nullable','string'],
            'visita_general.nombres'                   => ['sometimes','nullable','string'],
            'visita_general.apellidos'                 => ['sometimes','nullable','string'],
            'visita_general.rut'                       => ['sometimes','nullable','string'],
            'visita_general.telefono'                  => ['sometimes','nullable','string'],
            'visita_general.firma'                     => ['sometimes','nullable','string'],
            'visita_general.observacion_primera_visita'=> ['sometimes','nullable','string'],

            'visita_inspeccion' => ['sometimes','array'],
            'visita_inspeccion.num_colmenas_totales'        => ['sometimes','nullable','integer'],
            'visita_inspeccion.num_colmenas_inspeccionadas' => ['sometimes','nullable','integer'],
            'visita_inspeccion.num_colmenas_enfermas'       => ['sometimes','nullable','integer'],
            'visita_inspeccion.num_colmenas_activas'        => ['sometimes','nullable','integer'],
            'visita_inspeccion.num_colmenas_muertas'        => ['sometimes','nullable','integer'],
            'visita_inspeccion.flujo_nectar_polen'          => ['sometimes','nullable','string'],
            'visita_inspeccion.nombre_revisor_apiario'      => ['sometimes','nullable','string'],
            'visita_inspeccion.sospecha_enfermedad'         => ['sometimes','nullable','string'],
            'visita_inspeccion.observaciones'               => ['sometimes','nullable','string'],
        ];
    }
}
