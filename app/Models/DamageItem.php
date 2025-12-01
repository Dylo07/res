<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageItem extends Model
{
    protected $fillable = [
        'item_name',
        'quantity',
        'unit_price',
        'total_cost',
        'type',
        'notes',
        'reported_date'
    ];

    protected $casts = [
        'reported_date' => 'date',
        'unit_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            $model->total_cost = $model->quantity * $model->unit_price;
        });
    }
}