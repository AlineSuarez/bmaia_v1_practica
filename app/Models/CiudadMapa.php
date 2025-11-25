<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CiudadMapa extends Model
{
    protected $table = 'ciudades_mapa';
    protected $fillable = ['region_id','nombre','slug','lat','lon','resumen'];

    public function region(): BelongsTo {
        return $this->belongsTo(RegionMapa::class, 'region_id');
    }
}
