<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegionMapa extends Model
{
    protected $table = 'regiones_mapa';
    protected $fillable = [
        'nombre','slug','svg_path','centro_lat','centro_lon','descripcion'
    ];

    public function ciudades(): HasMany {
        return $this->hasMany(CiudadMapa::class, 'region_id');
    }
}
