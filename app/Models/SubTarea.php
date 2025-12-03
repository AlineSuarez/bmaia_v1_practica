<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TareaGeneral;
use App\Models\User;
class SubTarea extends Model
{
    use HasFactory;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'subtareas';

    /**
     * Atributos que se pueden asignar en masa.
     *
     * @var array
     */
    protected $fillable = [
        'tarea_general_id', // Foreign key de Tareas Generales
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_limite',
        'estado', // Ej.: pendiente, completada, urgente
        'prioridad', // Ej.: pendiente, completada, urgente,
        'prioridad_base', // Prioridad original de la tarea
        'user_id',
        'archivada', // Campo para archivar tareas
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_limite' => 'datetime',
    ];

    /**
     * Relación con la tabla Tareas Generales.
     * Una subtarea pertenece a una Tarea General.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tareaGeneral()
    {
        return $this->belongsTo(TareaGeneral::class, 'tarea_general_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('archivada', false);
    }

    public function scopeArchivadas($query)
    {
        return $query->where('archivada', true);
    }

}