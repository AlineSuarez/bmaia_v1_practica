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
        'billing_snapshot',
        'expires_at',
        'card_details',
        'buy_order',
        'session_id'

    ];
    protected $casts = [
        'billing_snapshot' => 'array', // convierte JSON a array automáticamente
        'expires_at'      => 'datetime',
        'card_details'   => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function datosFacturacion()
    {
        return $this->belongsTo(\App\Models\DatoFacturacion::class, 'dato_facturacion_id');
    }
    public function factura()
    {
        return $this->hasOne(\App\Models\Factura::class, 'payment_id');
    }

}
