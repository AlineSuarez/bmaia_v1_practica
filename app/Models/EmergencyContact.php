<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    // Si lo tienes así, Laravel NUNCA podrá hacer mass‐assignment de ninguno de estos campos:
    // protected $guarded = ['*'];

    // O si no tienes ni $fillable ni $guarded, por defecto $guarded = ['*'].

    // Debes exponer explícitamente los campos que vas a asignar con create():
    protected $fillable = [
        'user_id',
        'name',
        'relation',
        'phone',
        'email',
        'address',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}