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

    protected $casts = [
        'fecha_introduccion' => 'date',
        'reemplazos_realizados' => 'array',
    ];

    public function visita()
    {
        return $this->belongsTo(\App\Models\Visita::class);
    }


}
