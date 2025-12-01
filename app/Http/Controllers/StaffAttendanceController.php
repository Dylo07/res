<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StaffAttendanceController extends Controller
{
    public function __construct()
    {
        // Ensure upload directory exists and is writable
        $tempPath = storage_path('app/temp');
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }
    }





    public function index(Request $request)
    {
        try {
            $staff = Staff::where('status', 'active')->orderBy('staff_code')->get();
            
            $selectedDate = $request->month 
                ? Carbon::createFromFormat('Y-m', $request->month)
                : Carbon::now();
            
            $attendances = Attendance::whereMonth('date', $selectedDate->month)
                ->whereYear('date', $selectedDate->year)
                ->get()
                ->groupBy(function($item) {
                    return $item->staff_id . '-' . Carbon::parse($item->date)->format('d');
                });
        
            Log::info('Loading attendance index', [
                'staff_count' => $staff->count(),
                'attendance_records' => $attendances->count(),
                'selected_month' => $selectedDate->format('Y-m'),
                'session_id' => session()->getId()
            ]);
        
            return view('staff.attendance.index', compact(
                'staff', 
                'attendances',
                'selectedDate'
            ));
        } catch (\Exception $e) {
            Log::error('Error in attendance index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading attendance data');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'staff_id' => 'required|exists:staff,id',
                'check_in' => 'required|date_format:H:i',
                'date' => 'required|date'
            ]);

            $attendance = Attendance::updateOrCreate(
                [
                    'staff_id' => $validated['staff_id'],
                    'date' => $validated['date']
                ],
                [
                    'check_in' => $validated['check_in'],
                    'status' => $this->calculateStatus($validated['check_in'])
                ]
            );

            Log::info('Attendance stored', ['attendance_id' => $attendance->id]);
            return response()->json(['success' => true, 'attendance' => $attendance]);
        } catch (\Exception $e) {
            Log::error('Error storing attendance', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error storing attendance'], 500);
        }
    }

    public function checkOut(Request $request)
    {
        try {
            $validated = $request->validate([
                'staff_id' => 'required|exists:staff,id',
                'check_out' => 'required|date_format:H:i',
                'date' => 'required|date'
            ]);

            $attendance = Attendance::where('staff_id', $validated['staff_id'])
                ->whereDate('date', $validated['date'])
                ->first();

            if ($attendance) {
                $attendance->update(['check_out' => $validated['check_out']]);
                Log::info('Checkout recorded', ['attendance_id' => $attendance->id]);
            }

            return response()->json(['success' => true, 'attendance' => $attendance]);
        } catch (\Exception $e) {
            Log::error('Error recording checkout', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error recording checkout'], 500);
        }
    }

    private function calculateStatus($checkInTime)
    {
        $startTime = Carbon::createFromTimeString('08:30:00');
        $checkIn = Carbon::createFromTimeString($checkInTime);
        return $checkIn->gt($startTime) ? 'late' : 'present';
    }

    public function report(Request $request)
    {
        try {
            $startDate = $request->start_date 
                ? Carbon::parse($request->start_date)
                : Carbon::now()->startOfMonth();
            
            $endDate = $request->end_date 
                ? Carbon::parse($request->end_date)
                : Carbon::now()->endOfMonth();
        
            $staff = Staff::where('status', 'active')->get();
            
            $attendances = Attendance::whereBetween('date', [
                    $startDate->format('Y-m-d'),
                    $endDate->format('Y-m-d')
                ])
                ->get()
                ->groupBy('staff_id');
            
            $totalDays = $endDate->diffInDays($startDate) + 1;
        
            $reportData = [];
            foreach($staff as $member) {
                $staffAttendances = $attendances->get($member->id, collect([]));
                
                $reportData[$member->id] = [
                    'name' => $member->name,
                    'total_days' => $totalDays,
                    'present' => $staffAttendances->where('status', 'present')->count(),
                    'late' => $staffAttendances->where('status', 'late')->count(),
                    'absent' => $totalDays - ($staffAttendances->where('status', 'present')->count() + 
                                            $staffAttendances->where('status', 'late')->count())
                ];
            }

            Log::info('Report generated', [
                'date_range' => "$startDate to $endDate",
                'staff_count' => count($reportData)
            ]);
        
            return view('staff.attendance.report', compact(
                'reportData', 
                'startDate', 
                'endDate'
            ));
        } catch (\Exception $e) {
            Log::error('Error generating report', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error generating attendance report');
        }
    }

    public function import(Request $request)
    {
        // Enable error reporting for debugging
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        Log::info('Starting import process', [
            'request_headers' => $request->headers->all(),
            'session_id' => session()->getId()
        ]);

        try {
            // Validate the incoming request
            $validated = $request->validate([
                'attendance_file' => [
                    'required',
                    'file',
                    'mimes:xlsx,xls',
                    'max:5120' // 5MB max
                ]
            ]);

            // Check if file exists and is valid
            if (!$request->hasFile('attendance_file') || !$request->file('attendance_file')->isValid()) {
                Log::error('Invalid file upload');
                throw new \Exception('The uploaded file is invalid or corrupted');
            }

            $file = $request->file('attendance_file');
            
            // Log file details
            Log::info('File details', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'error' => $file->getError()
            ]);

            // Generate a unique filename
            $filename = 'attendance_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Store file directly in storage path
            $filePath = storage_path('app/temp/' . $filename);
            
            // Move uploaded file
            if (!move_uploaded_file($file->getPathname(), $filePath)) {
                throw new \Exception('Failed to move uploaded file');
            }

            // Process the Excel file
            Excel::import(
                new AttendanceImport($request->month ?? Carbon::now()->format('Y-m')),
                $filePath
            );

            // Clean up
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            Log::info('Import completed successfully');

            // Redirect with success message
            return redirect()
                ->route('staff.attendance.index')
                ->with('success', 'Attendance data imported successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', [
                'errors' => $e->errors(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Import error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Import failed: ' . $e->getMessage())
                ->withInput();
        }
    }


}