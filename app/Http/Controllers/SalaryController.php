<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Person;
use App\Models\ManualAttendance;
use App\Models\Cost;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function index()
    {
        // Get staff
        $staff = DB::table('persons')
            ->join('staff_codes', 'persons.id', '=', 'staff_codes.person_id')
            ->where('staff_codes.is_active', 1)
            ->select('persons.*')
            ->groupBy('persons.id', 'persons.name', 'persons.created_at', 'persons.updated_at', 'persons.type', 'persons.basic_salary')
            ->orderBy('persons.name')
            ->get();

        $month = request('month', Carbon::now()->format('m'));
        $year = request('year', Carbon::now()->format('Y'));
        
        // Calculate salary advance period for selected month
        $periodStart = Carbon::create($year, $month, 10, 0, 0, 0);
        $periodEnd = Carbon::create($year, $month)->addMonth()->setDay(10)->setTime(23, 59, 59);

        // Fetch salary advances
        $salaryAdvances = Cost::with(['person', 'user'])
            ->where('group_id', 1)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalAdvance = $salaryAdvances->sum('amount');

        // Get attendance data (1st to end of month)
        $attendanceData = [];
        foreach ($staff as $employee) {
            $attendance = ManualAttendance::where('person_id', $employee->id)
                ->whereYear('attendance_date', $year)
                ->whereMonth('attendance_date', $month)
                ->get();

            if ($attendance->count() > 0) {
                $attendanceData[$employee->id] = [
                    'present' => $attendance->where('status', 'present')->count(),
                    'half' => $attendance->where('status', 'half')->count(),
                    'absent' => $attendance->where('status', 'absent')->count()
                ];
            }
        }

        // Get processed salaries
        $salaries = Salary::with('person')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        // Calculate periods for dropdown
        $periods = [];
        for ($i = 0; $i < 3; $i++) {
            $basePeriod = Carbon::create($year, $month)->subMonths($i);
            $pStart = Carbon::create($basePeriod->year, $basePeriod->month, 10, 0, 0, 0);
            $pEnd = $basePeriod->copy()->addMonth()->setDay(10)->setTime(23, 59, 59);

            $periods[] = [
                'start' => $pStart,
                'end' => $pEnd,
                'label' => $pStart->format('M d, Y') . ' - ' . $pEnd->format('M d, Y')
            ];
        }

        $selectedPeriod = request('period', 0);
        
        return view('salary.index', compact(
            'staff', 'salaries', 'month', 'year',
            'periods', 'selectedPeriod', 'salaryAdvances', 
            'totalAdvance', 'attendanceData'
        ));
    }

    public function updateBasicSalary(Request $request)
    {
        $person = Person::findOrFail($request->person_id);
        $person->basic_salary = $request->basic_salary;
        $person->save();

        return response()->json(['success' => true]);
    }

    public function calculate(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $personId = $request->person_id;
        
        $person = Person::findOrFail($personId);
        $basicSalary = $person->basic_salary;

        if (!$basicSalary) {
            return response()->json([
                'success' => false,
                'message' => 'Please assign basic salary first'
            ], 400);
        }

        // Calculate advance period
        $basePeriod = Carbon::create($year, $month, 1);
        if ($basePeriod->day > 10) {
            $periodStart = Carbon::create($basePeriod->year, $basePeriod->month, 10, 0, 0, 0);
            $periodEnd = Carbon::create($basePeriod->year, $basePeriod->month + 1, 10, 23, 59, 59);
        } else {
            $periodStart = Carbon::create($basePeriod->year, $basePeriod->month - 1, 10, 0, 0, 0);
            $periodEnd = Carbon::create($basePeriod->year, $basePeriod->month, 10, 23, 59, 59);
        }

        // Get attendance data
        $attendance = ManualAttendance::where('person_id', $personId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->get();

        // Get salary advance
        $salaryAdvance = Cost::with(['person', 'user'])
            ->where('group_id', 1)
            ->where('person_id', $personId)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->orderBy('created_at', 'desc')
            ->sum('amount') ?? 0;

        $presentDays = $attendance->where('status', 'present')->count();
        $halfDays = $attendance->where('status', 'half')->count();
        $absentDays = $attendance->where('status', 'absent')->count();
        $presentDays += $halfDays * 0.5;

        $lastDayOfMonth = Carbon::create($year, $month)->endOfMonth()->day;
        $markedDays = $attendance->count();

        if ($markedDays < $lastDayOfMonth) {
            $finalSalary = ($presentDays * $basicSalary / 30) - $salaryAdvance;
            return $this->processSalary(
                $personId, $month, $year, $basicSalary, $salaryAdvance, 
                $absentDays, $presentDays, $finalSalary, 
                "Partial month calculation: $presentDays days present"
            );
        }

        // Regular calculation with attendance rules
        $totalDaysOff = $absentDays + ($halfDays * 0.5);

        if ($totalDaysOff == 5) {
            $finalSalary = $basicSalary - $salaryAdvance;
        } elseif ($totalDaysOff < 5) {
            $additionalDays = 5 - $totalDaysOff;
            $dailyRate = $basicSalary / 30;
            $finalSalary = $basicSalary - $salaryAdvance + ($additionalDays * $dailyRate);
        } else {
            $excessDays = $totalDaysOff - 5;
            $dailyRate = $basicSalary / 25;
            $finalSalary = $basicSalary - $salaryAdvance - ($excessDays * $dailyRate);
        }

        return $this->processSalary(
            $personId, $month, $year, $basicSalary, $salaryAdvance, 
            $absentDays, $presentDays, $finalSalary,
            "Full month calculation with $totalDaysOff days off"
        );
    }

    private function processSalary($personId, $month, $year, $basicSalary, $salaryAdvance, 
                                 $absentDays, $presentDays, $finalSalary, $remarks)
    {
        $salary = new Salary([
            'person_id' => $personId,
            'month' => $month,
            'year' => $year,
            'basic_salary' => $basicSalary,
            'salary_advance' => $salaryAdvance,
            'days_off' => $absentDays,
            'present_days' => $presentDays,
            'final_salary' => $finalSalary,
            'remarks' => $remarks
        ]);

        $salary->save();

        return response()->json([
            'success' => true,
            'salary' => $salary
        ]);
    }

    public function generatePayslip(Request $request)
{
    $person = Person::findOrFail($request->person_id);
    $month = request('month', Carbon::now()->format('m'));
    $year = request('year', Carbon::now()->format('Y'));

    // Get attendance data
    $attendance = ManualAttendance::where('person_id', $person->id)
        ->whereMonth('attendance_date', $month)
        ->whereYear('attendance_date', $year)
        ->get();

    // Calculate salary advance period
    $periodStart = Carbon::create($year, $month, 10, 0, 0, 0);
    $periodEnd = Carbon::create($year, $month)->addMonth()->setDay(10)->setTime(23, 59, 59);

    // Get salary advance
    $salaryAdvance = Cost::where('group_id', 1)
        ->where('person_id', $person->id)
        ->whereBetween('created_at', [$periodStart, $periodEnd])
        ->sum('amount') ?? 0;

    $presentDays = $attendance->where('status', 'present')->count();
    $halfDays = $attendance->where('status', 'half')->count();
    $absentDays = $attendance->where('status', 'absent')->count();
    $presentDays += $halfDays * 0.5;
    $totalDaysOff = $absentDays + ($halfDays * 0.5);

    $lastDayOfMonth = Carbon::create($year, $month)->endOfMonth()->day;
    $markedDays = $attendance->count();

    // Calculate final salary based on conditions
    if ($markedDays < $lastDayOfMonth) {
        $finalSalary = ($presentDays * $person->basic_salary / 30) - $salaryAdvance;
        $remarks = "Partial month calculation: $presentDays days present";
    } else {
        if ($totalDaysOff == 5) {
            $finalSalary = $person->basic_salary - $salaryAdvance;
        } elseif ($totalDaysOff < 5) {
            $additionalDays = 5 - $totalDaysOff;
            $dailyRate = $person->basic_salary / 30;
            $finalSalary = $person->basic_salary - $salaryAdvance + ($additionalDays * $dailyRate);
        } else {
            $excessDays = $totalDaysOff - 5;
            $dailyRate = $person->basic_salary / 25;
            $finalSalary = $person->basic_salary - $salaryAdvance - ($excessDays * $dailyRate);
        }
        $remarks = "Full month calculation with $totalDaysOff days off";
    }

    // Create a temporary salary object (not saved to database)
    $salary = new Salary([
        'person_id' => $person->id,
        'month' => $month,
        'year' => $year,
        'basic_salary' => $person->basic_salary,
        'salary_advance' => $salaryAdvance,
        'days_off' => $absentDays,
        'present_days' => $presentDays,
        'final_salary' => $finalSalary,
        'remarks' => $remarks
    ]);

    $salary->person = $person;  // Set the relationship manually

    return view('salary.payslip', compact('salary'));
}
public function basicSalary()
{
    $staff = DB::table('persons')
        ->join('staff_codes', 'persons.id', '=', 'staff_codes.person_id')
        ->where('staff_codes.is_active', 1)
        ->select('persons.*')
        ->get();

    return view('salary.basic', compact('staff'));
}

public function updateBasic(Request $request)
{
    $person = Person::findOrFail($request->person_id);
    $person->basic_salary = $request->basic_salary;
    $person->save();

    return response()->json(['success' => true]);
}
public function payslip($id)
{
    $salary = Salary::with('person')->findOrFail($id);
    return view('salary.payslip', compact('salary'));
}

}