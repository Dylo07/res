<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageCategory extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    public function packages()
    {
        return $this->hasMany(Package::class, 'category_id');
    }
}