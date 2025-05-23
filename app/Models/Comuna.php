<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comuna extends Model
{
    use HasFactory;
    protected $table = 'comunas';
    protected $fillable = ['nombre', 'region_id',
    'lat', 'lon',
    'utm_x', 'utm_y', 'utm_huso',];

    // Relación inversa: una comuna pertenece a una región
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
