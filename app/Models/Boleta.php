<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Boleta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'numero',
        'folio',
        'estado',
        'monto_neto',
        'monto_iva',
        'monto_total',
        'porcentaje_iva',
        'moneda',
        'fecha_emision',
        'fecha_vencimiento',
        'pdf_url',
        'datos_usuario_snapshot',
        'plan',
    ];

    protected $casts = [
        'datos_usuario_snapshot' => 'array',
        'fecha_emision'          => 'datetime',
        'fecha_vencimiento'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    // Accessor para mostrar el número de boleta formateado
    public function getNumeroMostrarAttribute()
    {
        return $this->numero ?? 'B-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
