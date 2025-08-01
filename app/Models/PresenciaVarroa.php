<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresenciaVarroa extends Model
{
    use HasFactory;
    protected $table = 'presencia_varroa';

    protected $fillable = [
        'colmena_id',
        'visita_id',
        'diagnostico_visual',
        'muestreo_abejas_adultas',
        'muestreo_cria_operculada',
        'tratamiento',
        'metodo_diagnostico',
        'fecha_monitoreo_varroa',
        'producto_comercial',
        'ingrediente_activo',
        'fecha_aplicacion',
        'dosificacion',
        'metodo_aplicacion',
        'periodo_carencia',
    ];
    protected $casts = [
      'fecha_aplicacion' => 'date',
    ];
    
    public function visita()
    {
        return $this->belongsTo(\App\Models\Visita::class);
    }

}
