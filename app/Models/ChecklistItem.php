<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    protected $fillable = ['name'];

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_checklist_items')
            ->withPivot('is_checked', 'daily_checked')
            ->withTimestamps();
    }
}