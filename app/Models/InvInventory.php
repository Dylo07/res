<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvInventory extends Model
{
    protected $table = 'inv_inventories';
    protected $fillable = ['product_id', 'stock_date', 'stock_level'];

    protected $casts = [
        'stock_level' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(InvProduct::class, 'product_id');
    }
}