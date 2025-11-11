<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use App\Models\HistorialCambios;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'nombreProducto',
        'cantidad',
        'unidad',
        'category_id',
        'precio',
        'observacion',
        'user_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class);
    }

    public function historialCambios(){
        return $this->hasMany(HistorialCambios::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
