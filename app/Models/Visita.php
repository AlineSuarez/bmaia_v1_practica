<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $fillable = [
        'apiario_id',
        'colmena_id', // Agregado
        'fecha_visita',
        'vigor_de_colmena',
        'actividad_colmena',
        'ingreso_pollen',
        'bloqueo_camara_cria',
        'presencia_celdas_reales',
        'postura_de_reina',
        'estado_de_cria',
        'postura_zanganos',
        'reserva_alimento',
        'presencia_varroa',
        'observaciones',
        'tipo_visita',
        'num_colmenas_totales' ,
        'num_colmenas_inspeccionadas',
        'num_colmenas_enfermas',
        'observacion_primera_visita',
        'num_colmenas_tratadas', // Agregado
        'motivo_tratamiento', // Agregado
        'nombre_comercial_medicamento', // Agregado
        'principio_activo_medicamento', // Agregado
        'periodo_resguardo', // Agregado
        'responsable', // Agregado
        'nombres', // Agregado
        'apellidos', // Agregado
        'rut', // Agregado
        'motivo', // Agregado
        'telefono', // Agregado
        'firma', // Agregado
    ];
    
    // Relaciones
    public function apiario()
    {
        return $this->belongsTo(Apiario::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}