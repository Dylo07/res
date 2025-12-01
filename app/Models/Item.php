<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items'; // Explicitly set the table name
    protected $fillable = ['name', 'group_id'];
    public function group()
    {
        return $this->belongsTo(ProductGroup::class, 'group_id');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'item_id');
    }

    public function logs()
    {
        return $this->hasMany(StockLog::class, 'item_id');
    }
}
