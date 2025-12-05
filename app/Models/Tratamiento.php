<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tratamiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tratamientos';

    protected $fillable = [
        'apiario_id',
        'colmena_id',
        'user_id',
        'fecha_aplicacion',
        'tipo_tratamiento',
        'medicamento',
        'nombre_comercial',
        'principio_activo',
        'dosis',
        'periodo_resguardo',
        'num_colmenas_tratadas',
        'responsable',
        'observaciones'
    ];

    protected $casts = [
        'fecha_aplicacion' => 'date',
        'num_colmenas_tratadas' => 'integer',
        'periodo_resguardo' => 'integer'
    ];

    // Relaciones
    public function apiario()
    {
        return $this->belongsTo(Apiario::class);
    }

    public function colmena()
    {
        return $this->belongsTo(Colmena::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
