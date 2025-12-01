<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCharge extends Model
{
    protected $fillable = [
        'person_id',
        'month',
        'year',
        'total_sc',
        'points_ratio',
        'final_amount',
        'remarks'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}