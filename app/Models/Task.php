<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user',              // Name of the user who created the task
        'date_added',        // Date when the task was created
        'task',              // Task description
        'task_category_id',  // ID of the related task category
        'person_incharge',   // The person responsible for the task
        'priority_order',    // Priority level of the task (High, Medium, Low)
        'is_done',           // Status of the task (0 = pending, 1 = done)
    ];

    /**
     * Relationship: Task belongs to TaskCategory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taskCategory()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    /**
     * Scope a query to filter only pending tasks.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('is_done', false);
    }

    /**
     * Scope a query to filter only completed tasks.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_done', true);
    }
}
