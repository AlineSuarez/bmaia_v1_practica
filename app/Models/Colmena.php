<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Colmena extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'apiario_id',
        'codigo_qr',
        'color_etiqueta',
        'numero',
        'historial',
        'estado_inicial',
        'numero_marcos',
        'observaciones',

    ];

    protected $casts = [
        'historial' => 'array',
    ];

    public function apiario()
    {
        return $this->belongsTo(Apiario::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoColmena::class);
    }
    public function alimentaciones()
    {
        return $this->hasMany(\App\Models\EstadoNutricional::class);
    }

    public function varroas()
    {
        return $this->hasMany(\App\Models\PresenciaVarroa::class);
    }

    public function nosemosis()
    {
        return $this->hasMany(\App\Models\PresenciaNosemosis::class);
    }

    public function sistemaExpertos()
    {
        return $this->hasMany(\App\Models\SistemaExperto::class);
    }

    public function visitas()
    {
        return $this->hasMany(\App\Models\Visita::class);
    }

    public function indiceCosecha()
    {
        return $this->hasMany(\App\Models\IndiceCosecha::class);
    }

    public function preparacionInvernada()
    {
        return $this->hasMany(\App\Models\PreparacionInvernada::class);
    }

    public function calidadReina()
    {
        return $this->hasOne(CalidadReina::class)->latestOfMany(); // si Laravel >= 8.x
    }
    
    public function desarrolloCria()
    {
        return $this->hasOne(\App\Models\DesarrolloCria::class)->latestOfMany();
    }
}
