<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Correct Carbon import

class Cost extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'person_id',
        'user_id',
        'amount',
        'description',
        'cost_date',
    ];

    protected $casts = [
        'cost_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Ensure cost_date is always a Carbon instance
    public function getCostDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper method to format amount
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    // Helper method to format date
    public function getFormattedDateAttribute()
    {
        return $this->cost_date ? $this->cost_date->format('M d, Y') : 'N/A';
    }

    public static function getMonthlyDistribution($startDate, $endDate)
{
    $totalExpenses = self::whereBetween('cost_date', [$startDate, $endDate])
        ->sum('amount');

    return self::join('groups', 'costs.group_id', '=', 'groups.id')
        ->whereBetween('cost_date', [$startDate, $endDate])
        ->groupBy('groups.name')
        ->selectRaw('groups.name, SUM(amount) as total, (SUM(amount) / ? * 100) as percentage', [$totalExpenses])
        ->orderByDesc('total')
        ->get()
        ->map(function ($item) {
            return [
                'name' => $item->name,
                'value' => round($item->percentage, 1),
                'total' => $item->total
            ];
        });
}
}