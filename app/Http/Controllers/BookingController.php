<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Fetch all bookings for the calendar.
     */
    public function index()
    {
        $bookings = Booking::with('payments')->get();
        
        return $bookings->map(function ($booking) {
            $payments = $booking->payments->map(function ($payment) {
                return [
                    'amount' => $payment->amount,
                    'billNumber' => $payment->bill_number,
                    'date' => $payment->payment_date->format('Y-m-d'),
                    'method' => $payment->payment_method
                ];
            });
    
            // Handle room_numbers as array
            $roomNumbers = is_array($booking->room_numbers) ? 
                implode(', ', $booking->room_numbers) : 
                $booking->room_numbers;
    
            return [
                'id' => $booking->id,
                'title' => $booking->name,
                'start' => $booking->start,
                'end' => $booking->end,
                'function_type' => $booking->function_type,
                'contact_number' => $booking->contact_number,
                'room_numbers' => json_encode($booking->room_numbers), // Encode the array as JSON
                'guest_count' => $booking->guest_count,
                'name' => $booking->name,
                'advancePayments' => $payments
            ];
        });
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'start' => 'required|date',
                'end' => 'nullable|date',
                'name' => 'required|string',
                'function_type' => 'required|string',
                'contact_number' => 'required|string',
                'room_numbers' => 'required|array',
                'guest_count' => 'required|string',
                'advance_payment' => 'required|numeric',
                'bill_number' => 'required|string',
                'advance_date' => 'required|date',
                'payment_method' => 'required|in:online,cash'
            ]);
    
            \DB::beginTransaction();
    
            // Create booking
            $booking = Booking::create([
                'start' => $validated['start'],
                'end' => $validated['end'],
                'name' => $validated['name'],
                'function_type' => $validated['function_type'],
                'contact_number' => $validated['contact_number'],
                'room_numbers' => $validated['room_numbers'],
                'guest_count' => $validated['guest_count'],
                'user_id' => auth()->id()
            ]);
    
            // Create payment record
            $booking->addPayment([
                'advance_payment' => $validated['advance_payment'],
                'bill_number' => $validated['bill_number'],
                'advance_date' => $validated['advance_date'],
                'payment_method' => $validated['payment_method']
            ]);
    
            \DB::commit();
            return response()->json(['message' => 'Booking created successfully'], 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Booking Creation Error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Update an existing booking.
     */
    public function update(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            // Base validation rules without dates
            $validationRules = [
                'name' => 'required|string',
                'function_type' => 'required|string',
                'contact_number' => 'required|string',
                'room_numbers' => 'required|array',
                'guest_count' => 'required|string',
            ];
    
            // Add date validation rules only if they are present in request
            if ($request->has('start')) {
                $validationRules['start'] = 'required|date';
                $validationRules['end'] = 'nullable|date';
            }
    
            // Add payment validation rules only if payment data is present
            if ($request->has('advance_payment')) {
                $validationRules += [
                    'advance_payment' => 'required|numeric',
                    'bill_number' => 'required|string',
                    'advance_date' => 'required|date',
                    'payment_method' => 'required|in:online,cash'
                ];
            }
    
            $validated = $request->validate($validationRules);
    
            // Update booking details - only update fields that are present in the request
            $updateData = array_intersect_key($validated, array_flip([
                'name', 'function_type', 'contact_number', 'room_numbers', 'guest_count'
            ]));
    
            // Only include dates if they were provided
            if ($request->has('start')) {
                $updateData['start'] = $validated['start'];
                $updateData['end'] = $validated['end'];
            }
    
            $booking->update($updateData);
    
            // Add new payment only if payment data is present
            if ($request->has('advance_payment')) {
                $booking->addPayment([
                    'advance_payment' => $validated['advance_payment'],
                    'bill_number' => $validated['bill_number'],
                    'advance_date' => $validated['advance_date'],
                    'payment_method' => $validated['payment_method']
                ]);
            }
    
            return response()->json(['message' => 'Booking updated successfully']);
        } catch (\Exception $e) {
            Log::error('Booking Update Error:', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function availableRooms(Request $request)
    {
        $startDate = $request->query('date');
        $endDate = $request->query('endDate', $startDate);
        $excludeBookingId = $request->query('excludeBooking');
    
        if (!$startDate) {
            return response()->json([], 400);
        }
    
        $allRooms = [
            'Ahala', 'Sepalika', 'Sudu Araliya', 'Orchid', 'Olu', 'Nelum', 'Hansa',
            'Mayura', 'Lihini', '121', '122', '123', '124', '106', '107', '108',
            '109', 'CH Room', '130', '131', '132', '133', '134', '101', '102', 
            '103', '104', '105',
        ];
    
        // Get bookings for the date range
        $bookings = Booking::where(function ($query) use ($startDate, $endDate, $excludeBookingId) {
            $query->where(function ($q) use ($startDate, $endDate) {
                // Check for bookings with end date
                $q->where('start', '<=', $endDate)
                  ->where('end', '>=', $startDate)
                  ->where('end', '!=', 'null');
                  
                // OR bookings with no end date (single day bookings)
                $q->orWhere(function($sq) use ($startDate) {
                    $sq->whereDate('start', $startDate)
                       ->where(function($ssq) {
                           $ssq->whereNull('end')
                               ->orWhere('end', 'N/A')
                               ->orWhere('end', '');
                       });
                });
            });
            
            if ($excludeBookingId) {
                $query->where('id', '!=', $excludeBookingId);
            }
        })->get();
    
        // Get all booked rooms from the bookings
        $bookedRooms = [];
        foreach ($bookings as $booking) {
            $roomArray = $booking->room_numbers;
            
            // If it's a string, try to decode it
            if (is_string($roomArray)) {
                $roomArray = json_decode($roomArray, true);
            }
            
            // If it's an array, process each room
            if (is_array($roomArray)) {
                foreach ($roomArray as $room) {
                    $room = trim($room, '"\'[] ');
                    $bookedRooms[] = $room;
                }
            }
        }
    
        // Clean and filter booked rooms
        $bookedRooms = array_unique(array_filter($bookedRooms));
    
        // Debug logging
        \Log::info('Date Range:', ['start' => $startDate, 'end' => $endDate]);
        \Log::info('Bookings found:', $bookings->toArray());
        \Log::info('Booked Rooms:', $bookedRooms);
    
        $availableRooms = array_values(array_diff($allRooms, $bookedRooms));
    
        return response()->json($availableRooms);
    }
    public function getLogs()
{
    $logs = Booking::with('user')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($booking) {
            return [
                'function_type' => $booking->function_type,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
                'user_name' => $booking->user ? $booking->user->name : null,
                'advance_payment' => $booking->advance_payment,
                'bill_number' => $booking->bill_number,
                'advance_date' => $booking->advance_date,
                'payment_method' => $booking->payment_method,
                'guest_count' => $booking->guest_count,
                'start' => $booking->start,
                'end' => $booking->end,
            ];
        });

    return response()->json($logs);
}


public function printConfirmation($id)
{
    $booking = Booking::with(['payments' => function($query) {
        $query->orderBy('payment_date', 'asc'); // Order payments by date
    }])->findOrFail($id);
    // Get the latest payment
    $latestPayment = $booking->payments->last();
    
    $data = [
        'booking' => $booking,
        'payment' => $latestPayment,
        'isWedding' => $booking->function_type === 'Wedding'
    ];
    
    return view('bookings.print-confirmation', $data);
}

};
