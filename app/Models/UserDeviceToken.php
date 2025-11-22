<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDeviceToken extends Model
{
    protected $fillable = [
        'user_id', 
        'device_name', 
        'platform', 
        'fcm_token', 
        'last_used_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
