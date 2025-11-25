<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlorImagen extends Model
{
    use HasFactory;

    protected $table = 'flor_imagenes';
    protected $fillable = ['flor_id','fenofase_id','path','titulo','descripcion'];
    public $timestamps = false;

    public function flor()
    {
        return $this->belongsTo(Flor::class);
    }

    public function fenofase()
    {
        return $this->belongsTo(Fenofase::class);
    }

    // URL pública (asumiendo storage:link ya hecho)
    public function getUrlAttribute()
    {
        return asset('storage/'.$this->path);
    }
}
