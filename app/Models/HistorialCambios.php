<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistorialCambios extends Model
{
    //
    use HasFactory;


    public $timestamps = false;

    protected $fillable = [
        'inventory_id',
        'user_id',
        'precio',
        'cantidad',
        'fecha_actualizacion',
    ];

    public function inventory(){
        return $this->belongsTo(Inventory::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
