<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InMenu extends Model
{
    use HasFactory;
    use HasFactory;
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function inStock(){
        return $this->hasmany(InStock::class);
    }
}
