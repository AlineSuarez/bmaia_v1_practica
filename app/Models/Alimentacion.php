<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alimentacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'alimentacion';

    protected $fillable = [
        'apiario_id',
        'user_id',
        'fecha_aplicacion',
        'tipo_alimentacion',
        'objetivo',
        'insumo_utilizado',
        'dosificacion',
        'metodo_utilizado',
        'cantidad_kg',
        'num_colmenas',
        'observaciones'
    ];

    protected $casts = [
        'fecha_aplicacion' => 'date',
        'cantidad_kg' => 'decimal:2',
        'num_colmenas' => 'integer'
    ];

    // Relaciones
    public function apiario()
    {
        return $this->belongsTo(Apiario::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
