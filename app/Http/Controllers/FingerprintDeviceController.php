<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FingerprintDeviceController extends Controller
{
    public function processAttendance(Request $request)
    {
        $request->validate([
            'user_id' => 'required',      // Maps to your User ID
            'staff_code' => 'required',    // Maps to your Staff Code
            'punch_time' => 'required',    // Timestamp from device
            'device_id' => 'required'
        ]);

        
    // Find staff by their code
    $staff = Staff::where('staff_code', $request->staff_code)
    ->orWhere('id', $request->user_id)
    ->first();

if (!$staff) {
return response()->json(['error' => 'Staff not found'], 404);
}

$timestamp = Carbon::parse($request->punch_time);
$currentDate = $timestamp->format('Y-m-d');

        // Check if attendance exists for today
        $attendance = Attendance::where('staff_id', $staff->id)
            ->whereDate('date', $currentDate)
            ->first();

        if (!$attendance) {
            // Create check-in record
            $attendance = Attendance::create([
                'staff_id' => $staff->id,
                'date' => $currentDate,
                'check_in' => $timestamp->format('H:i:s'),
                'fingerprint_data' => $request->fingerprint_data,
                'device_id' => $request->device_id,
                'status' => $this->calculateStatus($timestamp->format('H:i:s'))
            ]);
        } else {
            // Update as check-out
            $attendance->update([
                'check_out' => $timestamp->format('H:i:s')
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => !$attendance->check_out ? 'Check-in recorded' : 'Check-out recorded',
            'data' => $attendance
        ]);
    }

    private function calculateStatus($checkInTime)
    {
        $startTime = Carbon::createFromTimeString('08:30:00');
        $checkIn = Carbon::createFromTimeString($checkInTime);

        return $checkIn->gt($startTime) ? 'late' : 'present';
    }
}