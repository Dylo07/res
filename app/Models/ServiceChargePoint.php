<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceChargePoint extends Model
{
    protected $fillable = ['person_id', 'points'];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}