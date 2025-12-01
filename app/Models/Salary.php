<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'person_id',
        'month',
        'year',
        'basic_salary',
        'salary_advance',
        'days_off',
        'present_days',
        'final_salary',
        'remarks'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}