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
        /*
        'fecha_ultima_inspeccion',
        'fecha_ultima_reina',
        'fecha_ultima_recoleccion',
        'fecha_ultima_recoleccion_miel',
        'fecha_ultima_recoleccion_propolis',
        'fecha_ultima_recoleccion_abejas',
        'fecha_ultima_recoleccion_cera',
        'fecha_ultima_recoleccion_jalea',
        'fecha_ultima_recoleccion_veneno',
        'fecha_ultima_recoleccion_polinizacion',
        'fecha_ultima_recoleccion_otros',
        'fecha_ultima_recoleccion_otros_texto',
        'fecha_ultima_recoleccion_otros_cantidad',
        'fecha_ultima_recoleccion_otros_unidad',
        'fecha_ultima_recoleccion_otros_observaciones',
        */
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
