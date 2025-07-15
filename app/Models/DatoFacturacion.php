<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatoFacturacion extends Model
{
    protected $table = 'datos_facturacion';

    protected $fillable = [
        'user_id',
        'razon_social',
        'rut',
        'giro',
        'direccion_comercial',
        'region_id',
        'comuna_id',
        'ciudad',
        'telefono',
        'correo',
        'autorizacion_envio_dte',
        'correo_envio_dte',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }
}
