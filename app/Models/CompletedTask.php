<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompletedTask extends Model
{
    protected $table = 'completed_tasks';

    protected $fillable = [
        'user',
        'date_added',
        'task',
        'task_category_id',
        'person_incharge',
        'priority_order',
        'is_done',
    ];

    public function taskCategory()
    {
        return $this->belongsTo(TaskCategory::class);
    }
}
