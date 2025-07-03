<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndiceCosecha extends Model
{
    use HasFactory;
    protected $table = 'indice_cosecha';

    protected $fillable = [
        'madurez_miel',
        'num_alzadas',
        'marcos_miel',
        'colmena_id',
        'visita_id',
    ];

    public function visita()
    {
        return $this->belongsTo(\App\Models\Visita::class);
    }

}
