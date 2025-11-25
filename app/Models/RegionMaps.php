<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionMaps extends Model
{
    use HasFactory;

    // Eloquent por defecto usaría "region_maps", igual lo dejo explícito
    protected $table = 'region_maps';

    protected $fillable = [
        'nombre',     // "Antofagasta"
        'iso_code',   // "CL-AN" (mismo que el id del <path> del SVG)
        'slug_svg',   // opcional: "antofagasta"
    ];

    public function comunas()
    {
        return $this->hasMany(ComunaMaps::class, 'region_maps_id');
    }
}
