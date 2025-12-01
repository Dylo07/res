<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualAttendance extends Model
{
    use HasFactory;

    protected $table = 'manual_attendances';

    protected $fillable = [
       'person_id',
    'status',
    'attendance_date',
    'remarks',
    'marked_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}