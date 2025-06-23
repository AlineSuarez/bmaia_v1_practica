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

    public function apiarioBase()
    {
        return $this->belongsTo(Apiario::class, 'apiario_base_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoColmena::class);
    }
}
