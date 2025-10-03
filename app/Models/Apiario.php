<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comuna;

class Apiario extends Model
{
    use HasFactory;

    // Definir los atributos que pueden ser asignados masivamente
    protected $fillable = [
        'user_id',
        'temporada_produccion',
        'registro_sag',
        'num_colmenas',
        'tipo_apiario',
        'tipo_manejo',
        'objetivo_produccion',
        'region_id',
        'comuna_id',
        'latitud',
        'longitud',
        'localizacion',
        'nombre',
        'url',
        'activo',
        'es_temporal',
        'foto'
    ];

    protected static function booted()
    {
        static::creating(function ($apiario) {
            if (is_null($apiario->temporada_produccion)) {
                $apiario->temporada_produccion = (int) now()->year;
            }
        });
    }

    protected $casts = ['activo' => 'boolean'];

    /**
     * Relación: Un apiario pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visitas()
    {
        return $this->hasMany(Visita::class);
    }

    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function colmenas()
    {
        return $this->hasMany(Colmena::class);
    }

    public function movimientosDestino()
    {
        return $this->hasMany(MovimientoColmena::class, 'apiario_destino_id');
    }

    public function ultimoMovimientoDestino()
    {
        return $this->hasOne(MovimientoColmena::class, 'apiario_destino_id')
            ->where('tipo_movimiento', 'traslado')
            ->latest('fecha_movimiento');
    }


    public function getColmenasHistoricasAttribute()
    {
        return $this
            ->movimientosDestino()
            ->where('tipo_movimiento', 'traslado')
            ->count();
    }

    //metodos para archivar apiario temporal
    public function debeArchivarse()
    {
        if ($this->tipo_apiario !== 'trashumante' || !$this->activo) {
            return false;
        }
        foreach ($this->colmenas as $colmena) {
            $ultimo = $colmena->movimientos()
                ->latest('fecha_movimiento')
                ->first();
            if (!$ultimo || $ultimo->tipo_movimiento !== 'retorno') {
                return false;
            }
        }
        return true;
    }

    public function ultimaVisita()
    {
        return $this->hasOne(Visita::class)->latestOfMany('fecha_visita');
    }

    public function calidadesReina()
    {
        return $this->hasManyThrough(
            \App\Models\CalidadReina::class,
            \App\Models\Visita::class,
            'apiario_id',
            'visita_id',
            'id',
            'id'
        );
    }
}
