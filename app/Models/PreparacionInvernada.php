<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreparacionInvernada extends Model
{
    use HasFactory;
    protected $table = 'preparacion_invernada';

    protected $fillable = [
        'control_sanitario',
        'fusion_colmenas',
        'reserva_alimento',
    ];
    public function visita()
    {
        return $this->hasOne(\App\Models\Visita::class, 'preparacion_invernada_id');
    }
}
