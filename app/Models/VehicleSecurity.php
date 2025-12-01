<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleSecurity extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_number',
        'matter',
        'description',
        'room_numbers',
        'adult_pool_count',
        'kids_pool_count',
        'checkout_time',
        'temp_checkout_time',
        'temp_checkin_time',
    'is_temp_out',
        'team',
        'created_at',
        'is_note'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'checkout_time' => 'datetime',
        'temp_checkout_time' => 'datetime',
        'temp_checkin_time' => 'datetime',
    'is_temp_out' => 'boolean',
        'room_numbers' => 'json',
        'is_note' => 'boolean'
    ];

    public static function getMatterOptions()
    {
        return [
            'Office for - Functions',
            'Office for - Wedding',
            'Office for - Other',
            
            'Day out Package',
            'Night In Package',
            'Pool Only',
            'Room Only',
            'Restaurant ',

            'Chemicals',
            
            'Staff Transport',
            'Guest Pick-up/Drop',
            'Maintenance Work',
            
            'Photography Session',
            'DJ or Other Related Vehicles',
            'Other',
            
        ];
    }

    public static function getRoomOptions()
    {
        return [
            '107', '106', '108', '109', '121', '122', '123', '124',
            'orchid', 'ahela', 'sepalika', 'sudu araliya','Olu','Nelum',
            '130', '131', '132', '133', '134',
            'Hansa', 'Lihini', 'Mayura',
            '101', '102', '103', '104', '105'
        ];
    }
}