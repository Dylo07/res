<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['name', 'daily_checked', 'is_booked'];
    
    protected $casts = [
        'daily_checked' => 'boolean',
        'is_booked' => 'boolean'
    ];

    public function checklistItems()
    {
        return $this->belongsToMany(ChecklistItem::class, 'room_checklist_items')
            ->withPivot('is_checked')
            ->withTimestamps();
    }
}