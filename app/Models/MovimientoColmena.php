<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Apiario; // necesario para el boot()

class MovimientoColmena extends Model
{
    /**
     * La tabla real en la base de datos
     */
    protected $table = 'movimientos_colmenas';
    protected $dates = [
        'fecha_movimiento',
        'fecha_inicio_mov',
        'fecha_termino_mov',
    ];
    protected $casts = [
        'fecha_movimiento' => 'datetime',  // <-- así Laravel lo convierte en Carbon automáticamente
    ];

    protected $fillable = [
        'colmena_id',
        'apiario_origen_id',
        'apiario_destino_id',
        'tipo_movimiento',
        'fecha_movimiento',
        'fecha_inicio_mov',
        'fecha_termino_mov',
        'motivo_movimiento',
        'observaciones',
        'transportista',
        'vehiculo',
        'estado',
        'created_by',
        'updated_by'
    ];

    public static function booted()
    {
        static::created(function($mov) {
            if ($mov->tipo_movimiento === 'retorno') {
                $apiarioTemp = Apiario::find($mov->apiario_origen_id);
                if ($apiarioTemp && $apiarioTemp->debeArchivarse()) {
                    $apiarioTemp->update(['activo' => 0]);
                }
            }
        });
    }

    public function colmena()
    {
        return $this->belongsTo(Colmena::class);
    }

    public function apiarioOrigen()
    {
        return $this->belongsTo(Apiario::class, 'apiario_origen_id');
    }

    public function apiarioDestino()
    {
        return $this->belongsTo(Apiario::class, 'apiario_destino_id');
    }
    public function origen()
    {
        return $this->belongsTo(Apiario::class, 'apiario_origen_id');
    }
    public function destino()
    {
        return $this->belongsTo(Apiario::class, 'apiario_destino_id');
    }
}
