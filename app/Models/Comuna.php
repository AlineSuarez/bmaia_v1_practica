<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comuna extends Model
{
    // Nombre de la tabla (si en tu BD se llama "comunas")
    protected $table = 'comunas';

    // Campos protegidos (puedes dejarlo así si no vas a editar desde aquí)
    protected $guarded = [];

    // Si tu tabla NO usa created_at / updated_at, pon false
    public $timestamps = false;
}
