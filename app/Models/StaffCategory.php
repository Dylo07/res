<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffCategory extends Model
{
    use HasFactory;

    protected $table = 'staff_categories';

    protected $fillable = [
        'person_id',
        'category',
    ];

    /**
     * Get the person that this category belongs to
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}