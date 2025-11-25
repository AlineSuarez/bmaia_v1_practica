<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionPerfil extends Model
{
    protected $table = 'region_perfiles';

    protected $fillable = [
        'region_id',                 // nullable: referencia a tu tabla base de regiones (si existe)
        'nombre',
        'slug',
        'resumen',
        'bucket',                    // muy_baja|baja|moderada|alta|muy_alta|sin_info

        // si en tu migración existen estos campos técnicos, los dejamos listados:
        'bosque_nativo_ha',
        'plantaciones_forestales_ha',
        'bosque_nativo_pct',
        'plantaciones_forestales_pct',
        'amenaza_actual',
        'amenaza_futuro',
        'exposicion',
        'sensibilidad',
        'riesgo_actual',
        'riesgo_futuro',
    ];

    /**
     * Relación: un perfil de región tiene muchas comunas perfil.
     */
    public function comunas()
    {
        return $this->hasMany(ComunaPerfil::class, 'region_perfil_id');
    }

    /**
     * (Opcional) relación con tu tabla base de regiones si la usas.
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
