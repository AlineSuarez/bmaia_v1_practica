<?php
// app/Models/Alert.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'date',
        'priority',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
