<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Colmena extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'apiario_id',
        'codigo_qr',
        'color_etiqueta',
        'numero',
        'historial',
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
}
