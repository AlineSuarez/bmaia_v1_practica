<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComunaPerfil extends Model
{
    // Nombre de la tabla
    protected $table = 'comuna_perfiles';

    /**
     * Campos asignables en masa.
     * Ajusta los nombres si tu migración usa otros.
     */
    protected $fillable = [
        'region_perfil_id',   // FK a region_perfiles
        'nombre',             // ej: "Concepción"
        'slug',               // ej: "concepcion"
        'cod_externo',        // opcional: código INE/otro

        // métricas de ejemplo que usamos en el panel
        'bosques_nativos_ha',
        'plantaciones_ha',
        'pct_bosques',
        'pct_plantaciones',
        'notas',
    ];

    /**
     * Relación inversa: esta comuna pertenece a un perfil de región.
     */
    public function region()
    {
        return $this->belongsTo(RegionPerfil::class, 'region_perfil_id');
    }
}
