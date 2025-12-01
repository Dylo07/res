<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGroup extends Model
{
    protected $table = 'product_groups'; // Explicitly set the table name
    protected $fillable = ['name'];

    /**
     * Relationship with the Item model.
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'group_id');
    }
}
