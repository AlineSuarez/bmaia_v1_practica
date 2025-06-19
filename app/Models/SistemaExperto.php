<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SistemaExperto extends Model
{
    use HasFactory;

    protected $table = 'sistema_expertos';

    protected $fillable = [
        'apiario_id',
        'colmena_id',
        'fecha',
        'desarrollo_cria_id',
        'calidad_reina_id',
        'estado_nutricional_id',
        'presencia_varroa_id',
        'presencia_nosemosis_id',
        'indice_cosecha_id',
        'preparacion_invernada_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];


    // Relaciones
    public function apiario()
    {
        return $this->belongsTo(Apiario::class);
    }

    public function colmena()
    {
        return $this->belongsTo(Colmena::class);
    }

    public function desarrolloCria()
    {
        return $this->belongsTo(DesarrolloCria::class);
    }

    public function calidadReina()
    {
        return $this->belongsTo(CalidadReina::class);
    }

    public function estadoNutricional()
    {
        return $this->belongsTo(EstadoNutricional::class, 'estado_nutricional_id');
    }

    public function presenciaVarroa()
    {
        return $this->belongsTo(PresenciaVarroa::class);
    }

    public function presenciaNosemosis()
    {
        return $this->belongsTo(PresenciaNosemosis::class);
    }

    public function indiceCosecha()
    {
        return $this->belongsTo(IndiceCosecha::class);
    }

    public function preparacionInvernada()
    {
        return $this->belongsTo(PreparacionInvernada::class);
    }
}
