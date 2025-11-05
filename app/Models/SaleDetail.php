<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;
    
    /**
     * Get the sale that owns the sale detail.
     * (Your existing relationship - preserved)
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    
    /**
     * Get the menu item associated with this sale detail.
     * (NEW - This was missing and causing the error)
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}