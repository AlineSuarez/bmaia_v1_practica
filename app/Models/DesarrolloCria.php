<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesarrolloCria extends Model
{
    use HasFactory;
    protected $table = 'desarrollo_cria';

    protected $fillable = [
        'vigor_colmena',
        'actividad_abejas',
        'ingreso_polen',
        'bloqueo_camara_cria',
        'presencia_celdas_reales',
        // campos nuevos
        'cantidad_marcos_con_cria',
        'cantidad_marcos_con_abejas',
        'cantidad_reservas',
        'presencia_zanganos',
    ];
    
    public function visita()
    {
        return $this->hasOne(\App\Models\Visita::class, 'desarrollo_cria_id');
    }

}
