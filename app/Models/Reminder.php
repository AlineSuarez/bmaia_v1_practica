<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'remind_at',
        'notes',
        'repeat',
    ];

    /**
     * Cada recordatorio pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}