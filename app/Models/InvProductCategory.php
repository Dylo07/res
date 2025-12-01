<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvProductCategory extends Model
{
    protected $table = 'inv_product_categories';
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(InvProduct::class, 'category_id');
    }
}