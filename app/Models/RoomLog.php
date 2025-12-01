<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomLog extends Model
{
    protected $fillable = ['room_id', 'user_id', 'action', 'details'];
    
    protected $casts = [
        'details' => 'json'
    ];

    // Relationship with Room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}