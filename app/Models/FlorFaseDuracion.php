<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlorFaseDuracion extends Model
{
    protected $table = 'flor_fase_duraciones';

    protected $fillable = [
        'flor_id',       // FK a flores.id
        'fase_clave',    // boton|inicio|plena|terminal (boton no es obligatorio, ver nota)
        'offset_dias',   // días desde 'boton' hasta esta fase
        'fuente',
        'nota',
    ];

    public function flor()
    {
        return $this->belongsTo(Flor::class);
    }
}
