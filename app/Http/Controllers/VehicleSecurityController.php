<?php

namespace App\Http\Controllers;

use App\Models\VehicleSecurity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VehicleSecurityController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $vehicles = VehicleSecurity::where(function($query) use ($today) {
            $query->where(function($q) {
                // Show all unchecked vehicles
                $q->whereNull('checkout_time')
                  ->where('is_note', false);
            })->orWhere(function($q) use ($today) {
                // Show vehicles checked out today
                $q->whereDate('checkout_time', $today)
                  ->where('is_note', false);
            })->orWhere(function($q) use ($today) {
                // Show today's new entries
                $q->whereDate('created_at', $today)
                  ->where('is_note', false);
            })->orWhere(function($q) use ($today) {
                // Show only today's notes
                $q->whereDate('created_at', $today)
                  ->where('is_note', true);
            });
        })->latest()->get();
    
        // Get all occupied rooms from unchecked out vehicles
        $occupiedRooms = VehicleSecurity::whereNull('checkout_time')
            ->whereNotNull('room_numbers')
            ->where('is_note', false)
            ->get()
            ->pluck('room_numbers')
            ->map(function($rooms) {
                return json_decode($rooms);
            })
            ->flatten()
            ->unique();
    
        // Get available rooms
        $allRooms = VehicleSecurity::getRoomOptions();
        $availableRooms = array_values(array_diff($allRooms, $occupiedRooms->toArray()));
    
        return view('vehicle-security.index', [
            'vehicles' => $vehicles,
            'matterOptions' => VehicleSecurity::getMatterOptions(),
            'roomOptions' => VehicleSecurity::getRoomOptions(),
            'availableRooms' => $availableRooms
        ]);
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string',
            'matter' => 'required|string',
            'description' => 'nullable|string',
            'room_numbers' => 'nullable|json',
            'adult_pool_count' => 'nullable|integer|min:0',
            'kids_pool_count' => 'nullable|integer|min:0',
            'is_note' => 'boolean'
        ]);
    
        $validated['is_note'] = $request->input('is_note', false);
    
        $vehicle = VehicleSecurity::create($validated);
    
        return response()->json([
            'success' => true,
            'message' => $validated['is_note'] ? 'Note saved successfully' : 'Vehicle entry created successfully',
            'vehicle' => $vehicle
        ]);
    }

    public function showByDate($date = null)
{
    try {
        // If no date provided, use today's date
        $selectedDate = $date ? Carbon::parse($date) : Carbon::today();
        
        $vehicles = VehicleSecurity::where(function($query) use ($selectedDate) {
            $query->where(function($q) {
                // Show all unchecked vehicles
                $q->whereNull('checkout_time')
                  ->where('is_note', false);
            })->orWhere(function($q) use ($selectedDate) {
                // Show vehicles checked out on selected date
                $q->whereDate('checkout_time', $selectedDate)
                  ->where('is_note', false);
            })->orWhere(function($q) use ($selectedDate) {
                // Show selected date's new entries
                $q->whereDate('created_at', $selectedDate)
                  ->where('is_note', false);
            })->orWhere(function($q) use ($selectedDate) {
                // Show only selected date's notes
                $q->whereDate('created_at', $selectedDate)
                  ->where('is_note', true);
            });
        })->latest()->get();

        // Get all occupied rooms from unchecked out vehicles
        $occupiedRooms = VehicleSecurity::whereNull('checkout_time')
            ->whereNotNull('room_numbers')
            ->where('is_note', false)
            ->get()
            ->pluck('room_numbers')
            ->map(function($rooms) {
                return json_decode($rooms) ?: [];
            })
            ->flatten()
            ->unique()
            ->values();

        // Get available rooms
        $allRooms = VehicleSecurity::getRoomOptions();
        $availableRooms = array_values(array_diff($allRooms, $occupiedRooms->toArray()));

        return view('vehicle-security.index', [
            'vehicles' => $vehicles,
            'selectedDate' => $selectedDate->format('Y-m-d'),
            'matterOptions' => VehicleSecurity::getMatterOptions(),
            'roomOptions' => VehicleSecurity::getRoomOptions(),
            'availableRooms' => $availableRooms
        ]);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Invalid date format');
    }
}

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string',
            'matter' => 'required|string',
            'description' => 'nullable|string',
           'room_numbers' => 'nullable|json',
            'adult_pool_count' => 'nullable|integer|min:0',
            'kids_pool_count' => 'nullable|integer|min:0',
        ]);

        $vehicle = VehicleSecurity::findOrFail($id);
        $vehicle->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle entry updated successfully',
            'vehicle' => $vehicle->fresh()
        ]);
    }

    public function checkout($id)
    {
        $vehicle = VehicleSecurity::findOrFail($id);
        $checkoutTime = now();
        $startTime = $vehicle->created_at;
        
        // Calculate duration in hours with decimal points
        $duration = $startTime->diffInMinutes($checkoutTime) / 60;
        $formattedDuration = number_format($duration, 1);
        
        $vehicle->update([
            'checkout_time' => $checkoutTime,
            'duration_hours' => $formattedDuration
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Vehicle checked out successfully',
            'checkout_time' => $checkoutTime->format('Y-m-d H:i'),
            'duration_hours' => $formattedDuration,
            'vehicle' => $vehicle->fresh()
        ]);
    }
    public function updateTeam(Request $request, $id)
    {
        $validated = $request->validate([
            'team' => 'nullable|string'
        ]);

        $vehicle = VehicleSecurity::findOrFail($id);
        $vehicle->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Team updated successfully',
            'vehicle' => $vehicle->fresh()
        ]);
    }


    public function tempCheckout($id)
{
    $vehicle = VehicleSecurity::findOrFail($id);
    $vehicle->update([
        'temp_checkout_time' => now(),
        'is_temp_out' => true,
        'temp_checkin_time' => null
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Vehicle temporarily checked out',
        'vehicle' => $vehicle->fresh()
    ]);
}

public function tempCheckin($id)
{
    $vehicle = VehicleSecurity::findOrFail($id);
    $vehicle->update([
        'temp_checkin_time' => now(),
        'is_temp_out' => false
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Vehicle checked back in',
        'vehicle' => $vehicle->fresh()
    ]);
}



    public function edit($id)
    {
        $vehicle = VehicleSecurity::findOrFail($id);
        return response()->json($vehicle);
    }


}