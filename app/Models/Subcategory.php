<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    //
    protected $guarded = [];
    public function inventories()
    {
        return $this->belongsToMany(Inventory::class);
    }
}

