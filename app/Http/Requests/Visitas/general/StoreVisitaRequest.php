<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Tus tests exigen auth en store/update/destroy. El middleware lo hará cumplir.
        return true;
    }

    public function rules(): array
    {
        return [
            'apiario_id' => ['required','integer','exists:apiarios,id'],
            'colmena_id' => ['nullable','integer','exists:colmenas,id'],

            // Acepta "fecha" (de los tests) o "fecha_visita" (tu DB). Al menos una requerida.
            'fecha'         => ['nullable','date'],
            'fecha_visita'  => ['nullable','date'],

            // Campos simples (todos opcionales para no chocar con payload mínimo del test)
            'observaciones' => ['nullable','string'],
            'tipo_visita'   => ['nullable','string'],
            'vigor_de_colmena'        => ['nullable','string'],
            'actividad_colmena'       => ['nullable','string'],
            'ingreso_pollen'          => ['nullable','string'],
            'bloqueo_camara_cria'     => ['nullable','string'],
            'presencia_celdas_reales' => ['nullable','string'],
            'postura_de_reina'        => ['nullable','string'],
            'estado_de_cria'          => ['nullable','string'],
            'postura_zanganos'        => ['nullable','string'],
            'reserva_alimento'        => ['nullable','string'],
            'presencia_varroa'        => ['nullable','string'],

            // Sub-formulario: Visita General (si viene)
            'visita_general' => ['sometimes','array'],
            'visita_general.motivo'                    => ['nullable','string'],
            'visita_general.nombres'                   => ['nullable','string'],
            'visita_general.apellidos'                 => ['nullable','string'],
            'visita_general.rut'                       => ['nullable','string'],
            'visita_general.telefono'                  => ['nullable','string'],
            'visita_general.firma'                     => ['nullable','string'],
            'visita_general.observacion_primera_visita'=> ['nullable','string'],

            // Sub-formulario: Inspección (si viene)
            'visita_inspeccion' => ['sometimes','array'],
            'visita_inspeccion.num_colmenas_totales'        => ['nullable','integer'],
            'visita_inspeccion.num_colmenas_inspeccionadas' => ['nullable','integer'],
            'visita_inspeccion.num_colmenas_enfermas'       => ['nullable','integer'],
            'visita_inspeccion.num_colmenas_activas'        => ['nullable','integer'],
            'visita_inspeccion.num_colmenas_muertas'        => ['nullable','integer'],
            'visita_inspeccion.flujo_nectar_polen'          => ['nullable','string'],
            'visita_inspeccion.nombre_revisor_apiario'      => ['nullable','string'],
            'visita_inspeccion.sospecha_enfermedad'         => ['nullable','string'],
            'visita_inspeccion.observaciones'               => ['nullable','string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            // Al menos una de las dos fechas debe venir
            if (!$this->filled('fecha') && !$this->filled('fecha_visita')) {
                $v->errors()->add('fecha', 'Se requiere la fecha (fecha o fecha_visita).');
            }
        });
    }
}
