<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'transaction_id',
        'status',
        'amount',
        'plan',
        'dato_facturacion_id',

    ];

    public function datosFacturacion()
    {
        return $this->belongsTo(\App\Models\DatoFacturacion::class, 'dato_facturacion_id');
    }
}
