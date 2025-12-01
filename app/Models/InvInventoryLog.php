<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvInventoryLog extends Model
{
    protected $fillable = ['product_id', 'user_id', 'action', 'quantity', 'description'];

    public function product()
    {
        return $this->belongsTo(InvProduct::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}