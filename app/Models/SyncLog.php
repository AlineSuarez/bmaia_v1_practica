<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $table = 'sync_logs';

    protected $fillable = [
        'uuid', 'entity', 'op', 'user_id', 'applied_at', 'meta',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'meta'       => 'array',
    ];
}
