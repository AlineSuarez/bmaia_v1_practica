<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'numero',
        'folio',
        'sii_track_id',
        'estado',
        'monto_neto',
        'monto_iva',
        'monto_total',
        'porcentaje_iva',
        'moneda',
        'fecha_emision',
        'fecha_vencimiento',
        // rutas absolutas (opcionales)
        'pdf_url',
        'xml_url',
        'datos_facturacion_snapshot',
        'plan',
        // rutas internas privadas (opcionales)
        'pdf_path',
        'xml_path',
    ];

    protected $casts = [
        'datos_facturacion_snapshot' => 'array',
        'fecha_emision'              => 'datetime',
        'fecha_vencimiento'          => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pago()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function getNumeroMostrarAttribute()
    {
        return $this->numero ?? $this->folio ?? '—';
    }
    
    public function getPdfUrlAttribute($value)
    {
        if (!empty($value)) {
            return $value; // URL absoluta ya guardada
        }

        $path = $this->attributes['pdf_path'] ?? null;
        if ($path) {
            // Si tienes S3 como cloud
            $disk = config('filesystems.cloud', 's3');

            // Si el disco soporta temporaryUrl (S3), úsalo
            try {
                return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes(5));
            } catch (\Throwable $e) {
                // Fallback: si estás en local/public, devolver URL pública
                if (Storage::disk($disk)->exists($path)) {
                    return Storage::disk($disk)->url($path);
                }
                // último intento: disco public
                if (Storage::disk('public')->exists($path)) {
                    return Storage::disk('public')->url($path);
                }
            }
        }
        return null;
    }

    public function getXmlUrlAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        $path = $this->attributes['xml_path'] ?? null;
        if ($path) {
            $disk = config('filesystems.cloud', 's3');
            try {
                return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes(5));
            } catch (\Throwable $e) {
                if (Storage::disk($disk)->exists($path)) {
                    return Storage::disk($disk)->url($path);
                }
                if (Storage::disk('public')->exists($path)) {
                    return Storage::disk('public')->url($path);
                }
            }
        }
        return null;
    }

    /* ===== Helpers para saber si hay archivo ===== */
    public function hasPdf(): bool
    {
        return (bool) (($this->attributes['pdf_url'] ?? null) || ($this->attributes['pdf_path'] ?? null));
    }

    public function hasXml(): bool
    {
        return (bool) (($this->attributes['xml_url'] ?? null) || ($this->attributes['xml_path'] ?? null));
    }

}
