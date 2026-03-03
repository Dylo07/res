<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuActivityLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'user_name',
        'menu_id',
        'menu_name',
        'action',
        'details',
        'old_price',
        'new_price'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
