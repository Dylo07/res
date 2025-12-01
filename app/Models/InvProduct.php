<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvProduct extends Model
{
    protected $fillable = ['name', 'category_id'];

    public function category()
    {
        return $this->belongsTo(InvCategory::class, 'category_id');
    }

    public function inventories()
    {
        return $this->hasMany(InvInventory::class, 'product_id');
    }
}