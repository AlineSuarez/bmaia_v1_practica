<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoNutricional extends Model
{
    use HasFactory;
    protected $table = 'estado_nutricional';

    protected $fillable = [
        'reserva_miel_polen',
        'tipo_alimentacion',
        'fecha_aplicacion',
        'insumo_utilizado',
        'dosifiacion', // OJO: en la bd esta escrito como dosifiacion
        'metodo_utilizado',
        'n_colmenas_tratadas',
    ];
    public function visita()
    {
        return $this->hasOne(\App\Models\Visita::class, 'estado_nutricional_id');
    }
}
