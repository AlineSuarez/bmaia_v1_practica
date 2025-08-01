<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoNutricional extends Model
{
    use HasFactory;
    protected $table = 'estado_nutricional';

    protected $fillable = [
        'colmena_id',
        'visita_id',
        'tipo_alimentacion',
        'fecha_aplicacion',
        'insumo_utilizado',
        'dosifiacion',
        'metodo_utilizado',
        'objetivo',
        
    ];
    
    protected $casts = [
    'fecha_aplicacion' => 'date',
    ];

    public function visita()
    {
        return $this->belongsTo(\App\Models\Visita::class);
    }
}

