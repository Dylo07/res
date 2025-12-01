<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = ['staff_code', 'name', 'status'];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}