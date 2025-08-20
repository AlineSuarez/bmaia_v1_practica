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
        'session_id',
        'doc_type',
        'response_code',
        'tbk_status',
        'payment_type',
        'auth_code',
        'expires_at',
        'receipt_issued_at',
        'receipt_items',
        'receipt_number',
        'receipt_paynment_method',
        'receipt_pdf_path'

    ];
    protected $casts = [
        'billing_snapshot' => 'array', // convierte JSON a array automáticamente
        'expires_at'      => 'datetime',
        'card_details'   => 'array',
        'receipt_issued_at' => 'datetime',
        'receipt_items'     => 'array',
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
