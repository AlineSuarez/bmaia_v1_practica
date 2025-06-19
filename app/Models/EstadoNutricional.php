<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoNutricional extends Model
{
    use HasFactory;
    protected $table = 'estado_nutricional';

    protected $fillable = [
        'tipo_alimentacion',
        'fecha_aplicacion',
        'insumo_utilizado',
        'dosifiacion',
        'metodo_utilizado',
        // nuevo
        'objetivo',
    ];
    public function visita()
    {
        return $this->hasOne(\App\Models\Visita::class, 'estado_nutricional_id');
    }
}

