<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    protected $fillable = [
        'user_id',
        'language',
        'date_format',
        'theme',
        'voice_preference',
        'default_view',
        'voice_match',
        'calendar_email',
        'calendar_push',
        'reminder_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}