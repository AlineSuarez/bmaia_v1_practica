<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlorPerfil extends Model
{
    protected $table = 'flor_perfiles';

    protected $fillable = [
        'flor_id','nombre_comun_alt','resumen','descripcion','habitat','distribucion',
        'nectar_score','polen_score','usos','fuente','enlace'
    ];

    public function flor()
    {
        return $this->belongsTo(Flor::class, 'flor_id');
    }
}
