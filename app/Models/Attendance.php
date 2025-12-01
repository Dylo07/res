<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'staff_id',
        'date',
        'raw_data',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at'
    ];

    // Define relationship with Staff model
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}