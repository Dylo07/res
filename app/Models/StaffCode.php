<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffCode extends Model
{
    use HasFactory;

    protected $table = 'staff_codes';

    protected $fillable = [
        'person_id',
        'staff_code',
        'is_active'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}