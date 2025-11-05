<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaApiario extends Model
{
    use HasFactory;

    protected $table = 'tareas_apiario';

    protected $fillable = [
        'apiario_id',
        'categoria_tarea',
        'tarea_especifica',
        'accion_realizada',
        'observaciones',
        'fecha_inicio',
        'fecha_termino',
        'proximo_seguimiento',
    ];

    public function apiario()
    {
        return $this->belongsTo(Apiario::class);
    }
}
