<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\UseSpec;
use Illuminate\Database\Eloquent\Model;


class InventarioPredefinido extends Model
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
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsToMany(Subcategory::class, 'inventario_predefinido_subcategory');
    }

}
