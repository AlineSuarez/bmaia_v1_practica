<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndiceCosecha extends Model
{
    use HasFactory;
    protected $table = 'indice_cosecha';

    protected $fillable = [
        'colmena_id',
        'visita_id',
        'madurez_miel',
        'num_alzadas',
        'marcos_miel',
        'id_lote_cosecha',
        'fecha_cosecha',
        'fecha_extraccion',
        'lugar_extraccion',
        'humedad_miel',
        'temperatura_ambiente',
        'responsable_cosecha',
        'notas',
    ];

    protected $casts = [
        'fecha_cosecha' => 'date',
        'fecha_extraccion' => 'date',
    ];
    public function visita()
    {
        return $this->belongsTo(\App\Models\Visita::class);
    }

    public function colmena()
    {
        return $this->belongsTo(\App\Models\Colmena::class);
    }
}
