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
        // nuevos diagnósticos / monitoreo
        'metodo_diagnostico',
        'fecha_monitoreo_varroa',
        // tratamientos
        'producto_comercial',
        'ingrediente_activo',
        'fecha_aplicacion',
        'dosificacion',
        'metodo_aplicacion',
        'periodo_carencia',
        'n_colmenas_tratadas',
    ];
    protected $casts = [
      'fecha_aplicacion' => 'date',
    ];
    public function visita()
    {
        return $this->hasOne(\App\Models\Visita::class, 'visita_id');
    }

}
