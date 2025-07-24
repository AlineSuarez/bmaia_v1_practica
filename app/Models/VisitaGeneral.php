<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitaGeneral extends Model
{
    use HasFactory;
    protected $table = 'visita_generales';

    protected $fillable = [
        'visita_id',
        'motivo',
        'nombres',
        'apellidos',
        'rut',
        'telefono',
        'firma',
        'observacion_primera_visita'
    ];

    public function visita()
    {
        return $this->belongsTo(Visita::class);
    }
}
