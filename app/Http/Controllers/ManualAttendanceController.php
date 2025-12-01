<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManualAttendance;
use App\Models\Person;
use App\Models\StaffCode;
use App\Models\StaffCategory; // New model for staff categories
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ManualAttendanceController extends Controller
{
   public function index()
    {
        // Get staff members who have staff codes using relationship
        $staffQuery = Person::whereHas('staffCode', function($query) {
                $query->where('is_active', 1);
            })
            ->where('type', 'individual')
            ->with('staffCategory'); // Eager load staff categories
        
        // Get all staff members
        $allStaff = $staffQuery->get();
        
        // Define the order of categories
        $categoryOrder = [
            'front_office',  // Front Office first
            'kitchen',
            'restaurant',
            'maintenance',
            'garden',
            'housekeeping',
            'laundry',
            'pool',
            null, // Uncategorized staff will be at the end
        ];
        
        // Group staff by category
        $staffByCategory = [];
        foreach ($categoryOrder as $category) {
            $staffByCategory[$category] = $allStaff->filter(function($staff) use ($category) {
                if ($category === null) {
                    // For null category, get staff with no category
                    return $staff->staffCategory === null;
                }
                return $staff->staffCategory && $staff->staffCategory->category === $category;
            })->sortBy('name')->values();
        }
        
        // Combine all staff in ordered categories
        $staff = collect();
        foreach ($staffByCategory as $categoryStaff) {
            $staff = $staff->concat($categoryStaff);
        }
        
        $today = Carbon::now()->format('Y-m-d');
        
        // Use attendance_date instead of created_at for accurate date retrieval
        $attendances = ManualAttendance::whereDate('attendance_date', $today)
                ->with('person')
                ->get()
                ->keyBy('person_id');
    
        // Add debug logging
        \Log::info('Staff count: ' . $staff->count());
        \Log::info('Staff by category count: ', collect($staffByCategory)->map->count()->toArray());
        \Log::info('Today\'s attendance count: ' . $attendances->count());
    
        // Define category names for display
        $categoryNames = [
            'front_office' => 'Front Office',
            'kitchen' => 'Kitchen',
            'restaurant' => 'Restaurant',
            'maintenance' => 'Maintenance',
            'garden' => 'Garden',
            'housekeeping' => 'Housekeeping',
            'garden' => 'Garden',
            'pool' => 'Pool',
            'laundry' => 'Laundry',
            null => 'Not Assigned'
        ];
    
        return view('attendance.manual.index', compact('staff', 'attendances', 'categoryNames', 'staffByCategory'));
    }
    public function markAttendance(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:persons,id',
            'status' => 'required|in:present,half,absent',
            'remarks' => 'nullable|string|max:255',
            'attendance_date' => 'required|date'
        ]);

        // Debug log to check input data
        \Log::info('Marking attendance', [
            'person_id' => $request->person_id,
            'status' => $request->status,
            'date' => $request->attendance_date,
            'now' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        // FIXED: Ensure we're using the app's timezone consistently
        $markDate = Carbon::parse($request->attendance_date, config('app.timezone'))->startOfDay();
        $now = Carbon::now(config('app.timezone'));
        
        // Only admin can mark attendance for previous dates
        if (!Auth::user()->checkAdmin() && $markDate->format('Y-m-d') !== $now->format('Y-m-d')) {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can mark attendance for previous dates'
            ], 403);
        }

        // Prevent marking attendance for future dates
        if ($markDate->isAfter($now)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot mark attendance for future dates'
            ], 403);
        }

        $attendance = ManualAttendance::updateOrCreate(
            [
                'person_id' => $request->person_id,
                'attendance_date' => $markDate->format('Y-m-d')
            ],
            [
                'status' => $request->status,
                'remarks' => $request->remarks,
                'marked_by' => Auth::id()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully',
            'attendance' => $attendance,
            'status_badge' => view('attendance.manual.partials.status-badge', [
                'status' => $attendance->status
            ])->render()
        ]);
    }

    /**
     * Toggle attendance between present/absent for a staff member on a selected date
     */
    public function toggleAttendance(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:persons,id',
            'attendance_date' => 'required|date'
        ]);

        // FIXED: Use consistent timezone
        $markDate = Carbon::parse($request->attendance_date, config('app.timezone'))->startOfDay();
        $now = Carbon::now(config('app.timezone'));
        
        // Debug log to track toggle actions
        \Log::info('Toggling attendance', [
            'person_id' => $request->person_id,
            'date' => $markDate->format('Y-m-d'),
            'current_time' => $now->format('Y-m-d H:i:s')
        ]);
        
        // Only admin can mark attendance for previous dates
        if (!Auth::user()->checkAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can toggle attendance for previous dates'
            ], 403);
        }

        // Prevent marking attendance for future dates
        if ($markDate->isAfter($now)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot mark attendance for future dates'
            ], 403);
        }

        // Find existing attendance record for this date and person
        $existingAttendance = ManualAttendance::where([
            'person_id' => $request->person_id,
            'attendance_date' => $markDate->format('Y-m-d')
        ])->first();

        // Determine the new status based on current status
        $newStatus = 'present'; // Default status for first click

        if ($existingAttendance) {
            // Toggle between present → half → absent → not marked (delete)
            if ($existingAttendance->status === 'present') {
                $newStatus = 'half';
            } elseif ($existingAttendance->status === 'half') {
                $newStatus = 'absent';
            } elseif ($existingAttendance->status === 'absent') {
                // Delete record (not marked)
                $existingAttendance->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance record removed',
                    'status' => 'not_marked'
                ]);
            }
        }

        // Update or create attendance record
        $attendance = ManualAttendance::updateOrCreate(
            [
                'person_id' => $request->person_id,
                'attendance_date' => $markDate->format('Y-m-d')
            ],
            [
                'status' => $newStatus,
                'remarks' => $existingAttendance->remarks ?? '',
                'marked_by' => Auth::id()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance toggled successfully',
            'attendance' => $attendance,
            'status' => $newStatus,
            'status_badge' => view('attendance.manual.partials.status-badge', [
                'status' => $attendance->status
            ])->render()
        ]);
    }
    
    /**
     * Get attendance history for a specific staff member
     */
    public function getStaffAttendanceHistory(Request $request, $personId)
    {
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);
        
        // Only admin can view attendance history
        if (!Auth::user()->checkAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only administrators can view attendance history'
            ], 403);
        }
        
        $person = Person::findOrFail($personId);
        
        // Set the month or default to current month
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        
        // FIXED: Improved query to ensure we're getting all attendance records correctly
        $attendances = ManualAttendance::where('person_id', $personId)
            ->whereBetween('attendance_date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->attendance_date)->format('Y-m-d');
            });
        
        // Debug for troubleshooting
        \Log::info("Staff attendance history for person {$personId}", [
            'month' => $month,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'records_found' => $attendances->count(),
            'dates' => $attendances->pluck('attendance_date', 'status')->toArray()
        ]);
        
        // Generate all dates in the month
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $dates[$dateKey] = [
                'date' => $dateKey,
                'formatted_date' => $currentDate->format('j M Y'),
                'day_name' => $currentDate->format('l'),
                'status' => isset($attendances[$dateKey]) ? $attendances[$dateKey]->status : 'not_marked',
                'remarks' => isset($attendances[$dateKey]) ? $attendances[$dateKey]->remarks : '',
            ];
            $currentDate->addDay();
        }
        
        return response()->json([
            'success' => true,
            'person' => $person,
            'dates' => $dates,
            'attendances' => $attendances
        ]);
    }

    public function showAddStaffForm()
    {
        $availablePersons = Person::whereDoesntHave('staffCode')
            ->orWhereHas('staffCode', function($query) {
                $query->where('is_active', 0);
            })
            ->where('type', 'individual')
            ->orderBy('name')
            ->get();
            
        return view('attendance.manual.add-staff', compact('availablePersons'));
    }

    public function showManageCategories()
    {
        // Get all active staff members
        $staff = Person::whereHas('staffCode', function($query) {
                $query->where('is_active', 1);
            })
            ->where('type', 'individual')
            ->orderBy('name')
            ->with('staffCategory') // Eager load staff categories
            ->get();

        // Available categories
        $categories = [
            'front_office' => 'Front Office',
            'garden' => 'Garden',
            'kitchen' => 'Kitchen',
            'maintenance' => 'Maintenance',
            'restaurant' => 'Restaurant',
            'housekeeping' => 'Housekeeping',
            'garden' => 'Garden',
            'pool' => 'Pool',
            'laundry' => 'Laundry',
        ];
        
        return view('attendance.manual.manage-categories', compact('staff', 'categories'));
    }


    public function addStaffMember(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:persons,id',
            'staff_code' => 'required|string|max:20',
            'staff_category' => 'required|string|in:front_office,garden,kitchen,maintenance,restaurant,housekeeping,laundry,pool',
        ]);

        // Only admin can add staff members
        if (!Auth::user()->checkAdmin()) {
            return redirect()->back()->with('error', 'Only administrators can add staff members');
        }

        // Start a transaction to ensure both staff code and category are saved
        DB::beginTransaction();
        
        try {
            // Check if person is already a staff member
            $existingStaff = StaffCode::where('person_id', $request->person_id)->first();
            
            if ($existingStaff) {
                // Update existing staff code
                $existingStaff->staff_code = $request->staff_code;
                $existingStaff->is_active = $request->has('is_active') ? 1 : 0;
                $existingStaff->save();
                
                // Update or create staff category
                StaffCategory::updateOrCreate(
                    ['person_id' => $request->person_id],
                    ['category' => $request->staff_category]
                );
            } else {
                // Create new staff code
                $staffCode = new StaffCode();
                $staffCode->person_id = $request->person_id;
                $staffCode->staff_code = $request->staff_code;
                $staffCode->is_active = $request->has('is_active') ? 1 : 0;
                $staffCode->save();
                
                // Create new staff category
                $staffCategory = new StaffCategory();
                $staffCategory->person_id = $request->person_id;
                $staffCategory->category = $request->staff_category;
                $staffCategory->save();
            }
            
            DB::commit();
            return redirect()->route('attendance.manual.index')->with('success', 'Staff member added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error adding staff member: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error adding staff member: ' . $e->getMessage())->withInput();
        }
    }
    public function updateStaffCategory(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:persons,id',
            'category' => 'required|string|in:front_office,garden,kitchen,maintenance,restaurant,housekeeping,laundry,pool',
        ]);

        // Only admin can update staff categories
        if (!Auth::user()->checkAdmin()) {
            return redirect()->back()->with('error', 'Only administrators can update staff categories');
        }

        try {
            // Update or create staff category
            StaffCategory::updateOrCreate(
                ['person_id' => $request->person_id],
                ['category' => $request->category]
            );

            return redirect()->back()->with('success', 'Staff category updated successfully');
        } catch (\Exception $e) {
            \Log::error('Error updating staff category: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating staff category: ' . $e->getMessage());
        }
    }

    public function bulkUpdateCategories(Request $request)
{
    // Basic validation
    $request->validate([
        'categories' => 'required|array'
    ]);

    // Only admin can update categories
    if (!Auth::user()->checkAdmin()) {
        return redirect()->back()->with('error', 'Only administrators can update staff categories');
    }

    $successCount = 0;
    $errors = [];

    // Process each category update individually (no transaction)
    foreach ($request->categories as $personId => $category) {
        // Skip if no category selected
        if (empty($category)) {
            continue;
        }

        try {
            // Check if the person exists
            $person = Person::find($personId);
            if (!$person) {
                $errors[] = "Person ID $personId not found";
                continue;
            }

            // Direct database insert/update using query builder
            // This bypasses Eloquent models which could be causing issues
            $exists = DB::table('staff_categories')
                ->where('person_id', $personId)
                ->exists();

            if ($exists) {
                // Update existing record
                DB::table('staff_categories')
                    ->where('person_id', $personId)
                    ->update([
                        'category' => $category,
                        'updated_at' => now()
                    ]);
            } else {
                // Insert new record
                DB::table('staff_categories')
                    ->insert([
                        'person_id' => $personId,
                        'category' => $category,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
            }

            $successCount++;
        } catch (\Exception $e) {
            \Log::error("Error updating category for person $personId: " . $e->getMessage());
            $errors[] = "Could not update category for " . ($person->name ?? "person #$personId") . ": " . $e->getMessage();
        }
    }

    // Prepare response message
    $message = "Updated $successCount staff categories successfully.";
    
    if (count($errors) > 0) {
        $message .= " However, there were " . count($errors) . " errors: " . implode("; ", $errors);
        return redirect()->back()->with('warning', $message);
    }

    return redirect()->back()->with('success', $message);
}

    /**
     * Bulk update staff categories
     */

    
    
    public function report(Request $request)
    {
        // Get staff members who have staff codes
        $staff = Person::whereHas('staffCode', function($query) {
            $query->where('is_active', 1);
        })
        ->where('type', 'individual')
        ->orderBy('name')
        ->get();

        // Default to current month if no dates selected
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        
        // Debug log for report date range
        \Log::info('Attendance report date range', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'staff_count' => $staff->count()
        ]);
        
        // Generate all dates in the range
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // FIXED: Improved query to ensure we're getting all attendance records correctly by date range
        $attendances = ManualAttendance::whereBetween('attendance_date', [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        ])
        ->when($request->staff_member, function($query) use ($request) {
            return $query->where('person_id', $request->staff_member);
        })
        ->when($request->staff_category, function($query) use ($request) {
            return $query->whereHas('person.staffCategory', function($q) use ($request) {
                $q->where('category', $request->staff_category);
            });
        })
        ->get();

        // Debug log for report data
        \Log::info('Attendance records found', [
            'total_records' => $attendances->count(),
            'date_range_days' => count($dates),
            'staff_member_filter' => $request->staff_member,
            'staff_category_filter' => $request->staff_category
        ]);

        // Create attendance map with proper initialization
        $attendanceMap = [];
        foreach ($dates as $date) {
            $attendanceMap[$date] = [];
            foreach ($staff as $member) {
                $attendanceMap[$date][$member->id] = null;
            }
        }

        // Fill in the attendance data
        foreach ($attendances as $attendance) {
            $date = $attendance->attendance_date->format('Y-m-d');
            if (isset($attendanceMap[$date])) {
                $attendanceMap[$date][$attendance->person_id] = $attendance;
            }
        }

        // Calculate summary
        $summary = [
            'total_present' => $attendances->where('status', 'present')->count(),
            'total_half' => $attendances->where('status', 'half')->count(),
            'total_absent' => $attendances->where('status', 'absent')->count()
        ];

        // Get list of categories for filter dropdown
        $categories = [
            'front_office' => 'Front Office',
            'garden' => 'Garden',
            'kitchen' => 'Kitchen',
            'maintenance' => 'Maintenance',
            'restaurant' => 'Restaurant',
            'housekeeping' => 'Housekeeping',
            'garden' => 'Garden',
            'pool' => 'Pool',
            'laundry' => 'Laundry'
        ];

        return view('attendance.manual.report', compact(
            'staff',
            'dates',
            'attendanceMap',
            'startDate',
            'endDate',
            'summary',
            'categories'
        ));
    }
/**
 * Display staff personal information page
 */
public function staffInformation(Request $request)
{
    // Get all active staff members with their personal information
    $staff = Person::whereHas('staffCode', function($query) {
        $query->where('is_active', 1);
    })
    ->where('type', 'individual')
    ->with(['staffCategory', 'staffCode'])
    ->orderBy('name')
    ->get();
    
    // Categories for filtering
    $categories = [
        'front_office' => 'Front Office',
        'garden' => 'Garden',
        'kitchen' => 'Kitchen',
        'maintenance' => 'Maintenance',
        'restaurant' => 'Restaurant',
        'housekeeping' => 'Housekeeping',
        'laundry' => 'Laundry',
        'pool' => 'Pool'
    ];
    
    // Apply category filter if provided
    if ($request->category && $request->category !== 'all') {
        $staff = $staff->filter(function($person) use ($request) {
            return $person->staffCategory && $person->staffCategory->category === $request->category;
        });
    }
    
    // Apply search filter if provided
    if ($request->search) {
        $searchTerm = strtolower($request->search);
        $staff = $staff->filter(function($person) use ($searchTerm) {
            return str_contains(strtolower($person->name), $searchTerm) ||
                   str_contains(strtolower($person->full_name ?? ''), $searchTerm) ||
                   str_contains(strtolower($person->id_card_number ?? ''), $searchTerm) ||
                   str_contains(strtolower($person->phone_number ?? ''), $searchTerm);
        });
    }
    
    return view('attendance.manual.staff-information', compact('staff', 'categories'));
}

/**
 * Display individual staff profile with personal details
 */
public function staffPersonalProfile($personId)
{
    $person = Person::with(['staffCategory', 'staffCode'])->findOrFail($personId);
    
    // Calculate age if date of birth is available
    $age = null;
    if ($person->date_of_birth) {
        $age = Carbon::parse($person->date_of_birth)->age;
    }
    
    // Calculate years of service if hire date is available
    $yearsOfService = null;
    if ($person->hire_date) {
        $yearsOfService = Carbon::parse($person->hire_date)->diffInYears(Carbon::now());
    }
    
    // Get recent attendance summary (last 30 days)
    $recentAttendance = ManualAttendance::where('person_id', $personId)
        ->where('attendance_date', '>=', Carbon::now()->subDays(30))
        ->get();
    
    $attendanceSummary = [
        'total_days' => $recentAttendance->count(),
        'present_days' => $recentAttendance->where('status', 'present')->count(),
        'half_days' => $recentAttendance->where('status', 'half')->count(),
        'absent_days' => $recentAttendance->where('status', 'absent')->count(),
    ];
    
    if ($attendanceSummary['total_days'] > 0) {
        $attendanceSummary['attendance_rate'] = round(
            (($attendanceSummary['present_days'] + ($attendanceSummary['half_days'] * 0.5)) / $attendanceSummary['total_days']) * 100, 
            2
        );
    } else {
        $attendanceSummary['attendance_rate'] = 0;
    }
    
    return view('attendance.manual.staff-personal-profile', compact(
        'person', 
        'age', 
        'yearsOfService', 
        'attendanceSummary'
    ));
}

/**
 * Show form to edit staff personal information
 */
public function editStaffPersonalInfo($personId)
{
    // REMOVED: Only admin can edit staff information
    // if (!Auth::user()->checkAdmin()) {
    //     return redirect()->back()->with('error', 'Only administrators can edit staff information');
    // }
    
    $person = Person::with(['staffCategory', 'staffCode'])->findOrFail($personId);
    
    $categories = [
        'front_office' => 'Front Office',
        'garden' => 'Garden',
        'kitchen' => 'Kitchen',
        'maintenance' => 'Maintenance',
        'restaurant' => 'Restaurant',
        'housekeeping' => 'Housekeeping',
        'laundry' => 'Laundry',
        'pool' => 'Pool'
    ];
    
    return view('attendance.manual.edit-staff-personal-info', compact('person', 'categories'));
}

/**
 * Update staff personal information
 */
public function updateStaffPersonalInfo(Request $request, $personId)
{
    // REMOVED: Only admin can update staff information
    // if (!Auth::user()->checkAdmin()) {
    //     return redirect()->back()->with('error', 'Only administrators can update staff information');
    // }
    
    $request->validate([
        'name' => 'required|string|max:255',
        'full_name' => 'nullable|string|max:255',
        'id_card_number' => 'nullable|string|max:50',
        'address' => 'nullable|string|max:500',
        'phone_number' => 'nullable|string|max:20',
        'emergency_contact' => 'nullable|string|max:255',
        'emergency_phone' => 'nullable|string|max:20',
        'date_of_birth' => 'nullable|date',
        'gender' => 'nullable|in:male,female,other',
        'position' => 'nullable|string|max:255',
        'hire_date' => 'nullable|date',
        'basic_salary' => 'nullable|numeric|min:0',
        'blood_group' => 'nullable|string|max:10',
        'email' => 'nullable|email|max:255',
        'notes' => 'nullable|string|max:1000',
        'staff_category' => 'nullable|string|in:front_office,garden,kitchen,maintenance,restaurant,housekeeping,laundry,pool',
        'staff_code' => 'nullable|string|max:20'
    ]);
    
    DB::beginTransaction();
    
    try {
        $person = Person::findOrFail($personId);
        
        // Update person information
        $person->update([
            'name' => $request->name,
            'full_name' => $request->full_name,
            'id_card_number' => $request->id_card_number,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'position' => $request->position,
            'hire_date' => $request->hire_date,
            'basic_salary' => $request->basic_salary,
            'blood_group' => $request->blood_group,
            'email' => $request->email,
            'notes' => $request->notes,
        ]);
        
        // Update staff category if provided
        if ($request->staff_category) {
            StaffCategory::updateOrCreate(
                ['person_id' => $personId],
                ['category' => $request->staff_category]
            );
        }
        
        // Update staff code if provided
        if ($request->staff_code) {
            StaffCode::updateOrCreate(
                ['person_id' => $personId],
                ['staff_code' => $request->staff_code, 'is_active' => 1]
            );
        }
        
        DB::commit();
        
        return redirect()->route('staff.personal.profile', $personId)
            ->with('success', 'Staff information updated successfully by ' . Auth::user()->name);
            
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error updating staff information: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Error updating staff information: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Show form to create new staff member with personal info
 */
public function createStaffPersonalInfo()
{
    // REMOVED: Only admin can create staff
    // if (!Auth::user()->checkAdmin()) {
    //     return redirect()->back()->with('error', 'Only administrators can create staff members');
    // }
    
    $categories = [
        'front_office' => 'Front Office',
        'garden' => 'Garden',
        'kitchen' => 'Kitchen',
        'maintenance' => 'Maintenance',
        'restaurant' => 'Restaurant',
        'housekeeping' => 'Housekeeping',
        'laundry' => 'Laundry',
        'pool' => 'Pool'
    ];
    
    return view('attendance.manual.create-staff-personal-info', compact('categories'));
}

/**
 * Store new staff member with personal information
 */

/**
 * Store new staff member with personal information (UPDATED - Remove admin check)
 */
public function storeStaffPersonalInfo(Request $request)
{
    // REMOVED: Only admin can create staff
    // if (!Auth::user()->checkAdmin()) {
    //     return redirect()->back()->with('error', 'Only administrators can create staff members');
    // }
    
    $request->validate([
        'name' => 'required|string|max:255',
        'full_name' => 'nullable|string|max:255',
        'id_card_number' => 'nullable|string|max:50|unique:persons,id_card_number',
        'address' => 'nullable|string|max:500',
        'phone_number' => 'nullable|string|max:20',
        'emergency_contact' => 'nullable|string|max:255',
        'emergency_phone' => 'nullable|string|max:20',
        'date_of_birth' => 'nullable|date',
        'gender' => 'nullable|in:male,female,other',
        'position' => 'nullable|string|max:255',
        'hire_date' => 'nullable|date',
        'basic_salary' => 'nullable|numeric|min:0',
        'blood_group' => 'nullable|string|max:10',
        'email' => 'nullable|email|max:255|unique:persons,email',
        'notes' => 'nullable|string|max:1000',
        'staff_category' => 'required|string|in:front_office,garden,kitchen,maintenance,restaurant,housekeeping,laundry,pool',
        'staff_code' => 'required|string|max:20|unique:staff_codes,staff_code'
    ]);
    
    DB::beginTransaction();
    
    try {
        // Create person
        $person = Person::create([
            'name' => $request->name,
            'full_name' => $request->full_name,
            'id_card_number' => $request->id_card_number,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'position' => $request->position,
            'hire_date' => $request->hire_date,
            'basic_salary' => $request->basic_salary,
            'blood_group' => $request->blood_group,
            'email' => $request->email,
            'notes' => $request->notes,
            'type' => 'individual'
        ]);
        
        // Create staff category
        StaffCategory::create([
            'person_id' => $person->id,
            'category' => $request->staff_category
        ]);
        
        // Create staff code
        StaffCode::create([
            'person_id' => $person->id,
            'staff_code' => $request->staff_code,
            'is_active' => 1
        ]);
        
        DB::commit();
        
        return redirect()->route('staff.information')
            ->with('success', 'Staff member created successfully by ' . Auth::user()->name);
            
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error creating staff member: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Error creating staff member: ' . $e->getMessage())
            ->withInput();
    }
}
/**
 * Export staff information to CSV
 */
public function exportStaffInformation(Request $request)
{
    $staff = Person::whereHas('staffCode', function($query) {
        $query->where('is_active', 1);
    })
    ->where('type', 'individual')
    ->with(['staffCategory', 'staffCode'])
    ->orderBy('name')
    ->get();
    
    $filename = 'staff_information_' . Carbon::now()->format('Y-m-d') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];
    
    $callback = function() use ($staff) {
        $file = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($file, [
            'Person ID',
            'Name',
            'Full Name',
            'ID Card Number',
            'Staff Code',
            'Category',
            'Position',
            'Phone Number',
            'Email',
            'Address',
            'Date of Birth',
            'Age',
            'Gender',
            'Blood Group',
            'Hire Date',
            'Years of Service',
            'Basic Salary',
            'Emergency Contact',
            'Emergency Phone',
            'Notes'
        ]);
        
        foreach ($staff as $person) {
            $age = $person->date_of_birth ? Carbon::parse($person->date_of_birth)->age : 'N/A';
            $yearsOfService = $person->hire_date ? Carbon::parse($person->hire_date)->diffInYears(Carbon::now()) : 'N/A';
            
            fputcsv($file, [
                $person->id,
                $person->name,
                $person->full_name ?? 'N/A',
                $person->id_card_number ?? 'N/A',
                $person->staffCode ? $person->staffCode->staff_code : 'N/A',
                $person->staffCategory ? ucfirst(str_replace('_', ' ', $person->staffCategory->category)) : 'Not Assigned',
                $person->position ?? 'N/A',
                $person->phone_number ?? 'N/A',
                $person->email ?? 'N/A',
                $person->address ?? 'N/A',
                $person->date_of_birth ? Carbon::parse($person->date_of_birth)->format('Y-m-d') : 'N/A',
                $age,
                $person->gender ? ucfirst($person->gender) : 'N/A',
                $person->blood_group ?? 'N/A',
                $person->hire_date ? Carbon::parse($person->hire_date)->format('Y-m-d') : 'N/A',
                $yearsOfService,
                $person->basic_salary ? 'Rs. ' . number_format($person->basic_salary, 2) : 'N/A',
                $person->emergency_contact ?? 'N/A',
                $person->emergency_phone ?? 'N/A',
                $person->notes ?? 'N/A'
            ]);
        }
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}

/**
 * Print ID card for staff member
 */
public function printIdCard($personId)
{
    $person = Person::with(['staffCategory', 'staffCode'])->findOrFail($personId);
    
    return view('attendance.manual.print-id-card', compact('person'));
}

public function logoutStaffSection()
{
    Session::forget('staff_section_authenticated');
    Session::forget('staff_section_auth_time');
    
    return redirect()->route('home')->with('success', 'Logged out from staff section successfully');
}
    
}