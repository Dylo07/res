<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'menu_items',
        'additional_info',
        'image'
    ];

    protected $casts = [
        'menu_items' => 'array',
        'additional_info' => 'array',
        'price' => 'decimal:2'
    ];

    public function category()
    {
        return $this->belongsTo(PackageCategory::class, 'category_id');
    }

    // Format price with two decimal places
    public function getPriceFormattedAttribute()
    {
        return number_format($this->price, 2);
    }

    // Get image URL with fallback
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/package-placeholder.jpg');
    }

    // Format menu items as string
    public function getMenuItemsAsStringAttribute()
    {
        if (!$this->menu_items) return '';
        return implode("\n", $this->menu_items);
    }

    // Format additional info as string
    public function getAdditionalInfoAsStringAttribute()
    {
        if (!$this->additional_info) return '';
        return collect($this->additional_info)
            ->map(function ($value, $key) {
                return "$key: $value";
            })->implode("\n");
    }
}