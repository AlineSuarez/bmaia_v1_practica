<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;

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

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function getNumeroMostrarAttribute()
    {
        return $this->numero ?? $this->folio ?? '—';
    }
    
    public function getPdfUrlAttribute($value)
    {
        if (!empty($value)) return $value;
        $path = $this->attributes['pdf_path'] ?? null;
        if (!$path) return null;

        /** @var FilesystemAdapter $public */
        $public = Storage::disk('public');
        if ($public->exists($path)) {
            return $public->url($path);
        }

        $cloudName = config('filesystems.cloud');
        if ($cloudName) {
            /** @var FilesystemAdapter $cloud */
            $cloud = Storage::disk($cloudName);

            if ($cloud->exists($path)) {
                // temporaryUrl solo existe en ciertos drivers (S3). Protegemos la llamada:
                if (method_exists($cloud, 'temporaryUrl')) {
                    try {
                        return $cloud->temporaryUrl($path, now()->addMinutes(5));
                    } catch (\Throwable $e) {
                        // si falla, seguimos al fallback url()
                    }
                }
                return $cloud->url($path);
            }
        }

        return null;
    }

    public function getXmlUrlAttribute($value)
    {
        if (!empty($value)) return $value;
        $path = $this->attributes['xml_path'] ?? null;
        if (!$path) return null;

        /** @var FilesystemAdapter $public */
        $public = Storage::disk('public');
        if ($public->exists($path)) {
            return $public->url($path);
        }

        $cloudName = config('filesystems.cloud');
        if ($cloudName) {
            /** @var FilesystemAdapter $cloud */
            $cloud = Storage::disk($cloudName);

            if ($cloud->exists($path)) {
                if (method_exists($cloud, 'temporaryUrl')) {
                    try {
                        return $cloud->temporaryUrl($path, now()->addMinutes(5));
                    } catch (\Throwable $e) {
                        // fallback a url() si no soporta temporal
                    }
                }
                return $cloud->url($path);
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
