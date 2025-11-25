<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComunaMaps extends Model
{
    use HasFactory;

    // tabla "comuna_maps"
    protected $table = 'comuna_maps';

    protected $fillable = [
        'region_maps_id',
        'nombre',
    ];

    public function region()
    {
        return $this->belongsTo(RegionMaps::class, 'region_maps_id');
    }
}
