<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InStock extends Model
{
    use HasFactory;
    public function inMenu(){
        return $this->belongsTo(Menu::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
