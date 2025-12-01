<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start', 
        'end', 
        
        'name', 
        'function_type',
        'contact_number',        
        'room_numbers',
        'guest_count',
        'user_id',
    ];
    

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
       'start' => 'datetime',
        'end' => 'datetime',
        'room_numbers' => 'array',
        
    ];
    public function setAdvancePaymentAttribute($value)
    {
        $this->attributes['advance_payment'] = $value ?? 0.00;
    }
    public function getFormattedAdvancePaymentAttribute()
    {
        return number_format($this->advance_payment, 2);
    }
/**
     * Check if rooms overlap with the given date.
     *
     * @param string $date
     * @return bool
     */
    public function overlapsWithDate($date)
    {
        return $this->start <= $date && $this->end >= $date;
    }

    /**
     * Scope for getting bookings that overlap with a given date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverlappingWithDate($query, $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->whereDate('start', '<=', $date)
              ->whereDate('end', '>=', $date);
        });
    }
    
    public function payments()
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function addPayment($data)
    {
        return $this->payments()->create([
            'amount' => $data['advance_payment'],
            'bill_number' => $data['bill_number'],
            'payment_date' => $data['advance_date'],
            'payment_method' => $data['payment_method']
        ]);
    }

    public function user()
{
    return $this->belongsTo(User::class);
}
// Get latest payment
public function getLatestPaymentAttribute()
{
    return $this->payments()->latest()->first();
}






}
