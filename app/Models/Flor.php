<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flor extends Model
{
    use HasFactory;

    protected $table = 'flores';

    // Incluyo 'slug' por si luego deseas crear/editar desde formularios.
    protected $fillable = ['nombre', 'nombre_cientifico', 'familia', 'slug'];

    public $timestamps = false;

    /**
     * Imágenes asociadas a la flor (por fenofase).
     */
    public function imagenes()
    {
        return $this->hasMany(FlorImagen::class, 'flor_id');
    }

    /**
     * Offsets/duraciones de fenofases para predicción.
     */
    public function faseDuraciones()
    {
        return $this->hasMany(\App\Models\FlorFaseDuracion::class, 'flor_id');
    }

    /**
     * Perfil informativo 1–a–1 para el Catálogo de Flora.
     */
    public function perfil()
    {
        return $this->hasOne(\App\Models\FlorPerfil::class, 'flor_id');
    }
}
