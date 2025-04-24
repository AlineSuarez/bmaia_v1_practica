<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresenciaNosemosis extends Model
{
    use HasFactory;
    protected $table = 'presencia_nosemosis';

    protected $fillable = [
        'signos_clinicos',
        'muestreo_laboratorio',
        'tratamiento',
        'fecha_aplicacion',
        'dosificacion',
        'metodo_aplicacion',
        'num_colmenas_tratadas',
    ];
    public function visita()
    {
        return $this->hasOne(\App\Models\Visita::class, 'presencia_nosemosis_id');
    }
}
