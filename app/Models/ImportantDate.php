<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportantDate extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'date',
        'recurs_annually',  // o como lo nombraste en tu migración
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}