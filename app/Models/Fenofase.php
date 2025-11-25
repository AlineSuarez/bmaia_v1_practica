<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fenofase extends Model
{
    use HasFactory;

    protected $table = 'fenofases';
    protected $fillable = ['clave','nombre','orden'];
    public $timestamps = false;

    public function imagenes()
    {
        return $this->hasMany(FlorImagen::class);
    }
}
