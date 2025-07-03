<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreparacionInvernada extends Model
{
    use HasFactory;
    protected $table = 'preparacion_invernada';

    protected $fillable = [
        'control_sanitario',
        'fusion_colmenas',
        'reserva_alimento',
        // nuevos
        'cantidad_marcos_cubiertos_abejas',
        'cantidad_marcos_cubiertos_cria',
        'marcos_reservas_miel',
        'presencial_reservas_polen',
        'presencia_reina',
        'nivel_infestacion_varroa',
        'signos_enfermedades_visibles',
        'fecha_ultima_revision_previa_receso',
        'fecha_cierre_temporada',
        'alimentacion_suplementaria',
    ];
    protected $casts = [
      'fecha_cierre_temporada'              => 'date',
      'fecha_ultima_revision_previa_receso' => 'date',
    ];

   public function visita()
    {
        return $this->belongsTo(\App\Models\Visita::class);
    }

}
