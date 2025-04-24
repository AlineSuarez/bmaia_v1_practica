<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresenciaVarroa extends Model
{
    use HasFactory;
    protected $table = 'presencia_varroa';

    protected $fillable = [
        'diagnostico_visual',
        'muestreo_abejas_adultas',
        'muestreo_cria_operculada',
        'tratamiento',
        'fecha_aplicacion',
        'dosificacion',
        'metodo_aplicacion',
        'n_colmenas_tratadas',
    ];
    public function visita()
    {
        return $this->hasOne(\App\Models\Visita::class, 'presencia_varroa_id');
    }

}
