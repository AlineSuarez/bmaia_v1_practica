<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitaInspeccion extends Model
{
    use HasFactory;
    protected $table = 'visita_inspecciones';

    protected $fillable = [
        'visita_id',
        'num_colmenas_totales',
        'num_colmenas_inspeccionadas',
        'num_colmenas_enfermas',
        'num_colmenas_activas',
        'num_colmenas_muertas',
        'flujo_nectar_polen',
        'nombre_revisor_apiario',
        'sospecha_enfermedad',
        'observaciones'
    ];

    public function visita()
    {
        return $this->belongsTo(Visita::class);
    }
}
