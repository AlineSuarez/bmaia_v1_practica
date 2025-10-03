<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Region extends Model
{
    use HasFactory;
    protected $table = 'regiones';

    protected $fillable = ['nombre', 'abreviatura'];

    protected static function booted()
    {
        static::creating(function ($region) {
            if (empty($region->abreviatura)) {
                // MAULE -> MAU (o ajusta la lógica si necesitas otra abreviatura)
                $region->abreviatura = Str::upper(Str::substr($region->nombre, 0, 3));
            }
        });
    }

    // Relación uno a muchos: una región tiene muchas comunas
    public function comunas()
    {
        return $this->hasMany(Comuna::class);
    }
}