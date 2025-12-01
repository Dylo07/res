<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RoomBooking extends Model
{
    protected $fillable = [
        'room_id',
        'guest_in_time',
        'guest_out_time',
    ];

    protected $casts = [
        'guest_in_time' => 'datetime',
        'guest_out_time' => 'datetime'
    ];

    protected $appends = ['stay_day_count', 'stay_status'];

    public function getStayDayCountAttribute()
    {
        if (!$this->guest_in_time) return 0;
        
        $checkDate = Carbon::parse(request('date', now()->format('Y-m-d')));
        $checkInDate = Carbon::parse($this->guest_in_time->format('Y-m-d'));
        
        // If check date is before check-in date, it's a future booking
        if ($checkDate->lt($checkInDate)) {
            return 0;
        }
        
        // Calculate days since check-in
        return $checkInDate->diffInDays($checkDate) + 1;
    }

    public function getStayStatusAttribute()
    {
        $dayCount = $this->stay_day_count;
        
        // Handle future bookings
        if ($dayCount === 0) {
            return 'Future Booking';
        }
        
        if ($dayCount === 1) {
            return 'Same Day';
        }
        
        $suffixes = [1 => 'st', 2 => 'nd', 3 => 'rd'];
        $suffix = isset($suffixes[$dayCount]) ? $suffixes[$dayCount] : 'th';
        
        return $dayCount . $suffix . ' day';
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}