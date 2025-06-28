<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresenciaNosemosis extends Model
{
    use HasFactory;
    protected $table = 'presencia_nosemosis';

    protected $fillable = [
        'colmena_id',
        'signos_clinicos',
        'muestreo_laboratorio',
        // nuevos
        'metodo_diagnostico_laboratorio',
        'fecha_monitoreo_nosema',
        'producto_comercial',
        'ingrediente_activo',
        'fecha_aplicacion',
        'dosificacion',
        'metodo_aplicacion',
    ];
    protected $casts = [
      'fecha_aplicacion' => 'date',
    ];
    
    public function visita()
    {
        return $this->hasOne(\App\Models\Visita::class, 'presencia_nosemosis_id');
    }
}
