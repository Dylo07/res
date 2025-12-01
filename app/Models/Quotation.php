<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'client_name',
        'client_address',
        'quotation_date',
        'schedule',
        'items',
        'service_charge',
        'total_amount',
        'comments'
    ];

    protected $casts = [
        'quotation_date' => 'date',
    'schedule' => 'date',
    'items' => 'array',
    'service_charge' => 'float',
    'total_amount' => 'float',
    'comments' => 'array',
    'status' => 'string'
    ];
}