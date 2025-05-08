<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'user_id',
        'notifications',
        'camera_access',
        'microphone',
        'location',
        'bluetooth',
    ];

    // Relación inversa (opcional)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
