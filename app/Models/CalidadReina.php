<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalidadReina extends Model
{
    use HasFactory;
    protected $table = 'calidad_reina';

    protected $fillable = [
        'postura_reina',
        'estado_cria',
        'postura_zanganos',
        // nuevos:
        'origen_reina',
        'raza',
        'linea_genetica',
        'fecha_introduccion',
        'estado_actual',
        'reemplazos_realizados',
    ];
    public function visita()
    {
        return $this->hasOne(\App\Models\Visita::class, 'calidad_reina_id');
    }

}
