<?php

namespace App\Http\Controllers;

use App\Models\ServiceCharge;
use App\Models\ServiceChargePoint;
use App\Models\Person;
use App\Models\Sale;
use App\Models\DamageItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ServiceChargeController extends Controller
{
    public function index()
{
    // Get active staff - Modified query to handle MySQL strict mode
    $staff = DB::table('persons')
        ->join('staff_codes', 'persons.id', '=', 'staff_codes.person_id')
        ->where('staff_codes.is_active', 1)
        ->select('persons.id', 'persons.name', 'persons.created_at', 'persons.updated_at', 'persons.type')
        ->groupBy('persons.id', 'persons.name', 'persons.created_at', 'persons.updated_at', 'persons.type')
        ->orderBy('persons.name')
        ->get();

    $month = request('month', Carbon::now()->format('m'));
    $year = request('year', Carbon::now()->format('Y'));

    // Get previous month's service charge
    $prevMonth = Carbon::create($year, $month)->subMonth();
    $prevMonthSales = Sale::whereMonth('updated_at', $prevMonth->month)
        ->whereYear('updated_at', $prevMonth->year)
        ->where('sale_status', 'paid')
        ->sum('total_recieved');

    // Get current month's damage items
    $currentMonthDamages = DamageItem::whereMonth('reported_date', $month)
        ->whereYear('reported_date', $year)
        ->sum('total_cost');

    // Calculate total giving SC
    $totalGivingSC = max(0, $prevMonthSales - $currentMonthDamages);

    // Get points for each person
    $points = ServiceChargePoint::with('person')->get();
    $totalPoints = $points->sum('points');

    // Get processed service charges
    $serviceCharges = ServiceCharge::with('person')
        ->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->get();

    // Generate months for dropdown
    $months = [];
    for ($i = 0; $i < 12; $i++) {
        $date = Carbon::now()->subMonths($i);
        $months[$date->format('Y-m')] = $date->format('F Y');
    }

    return view('service-charge.index', compact(
        'staff', 'points', 'totalPoints', 'totalGivingSC',
        'prevMonthSales', 'currentMonthDamages', 'serviceCharges',
        'month', 'year', 'months'
    ));
}

    public function updatePoints(Request $request)
    {
        $request->validate([
            'person_id' => 'required|exists:persons,id',
            'points' => 'required|integer|min:0'
        ]);

        ServiceChargePoint::updateOrCreate(
            ['person_id' => $request->person_id],
            ['points' => $request->points]
        );

        return response()->json(['success' => true]);
    }

    public function generateServiceCharge(Request $request)
    {
        $person = Person::findOrFail($request->person_id);
        $month = request('month', Carbon::now()->format('m'));
        $year = request('year', Carbon::now()->format('Y'));

        // Get previous month's service charge
        $prevMonth = Carbon::create($year, $month)->subMonth();
        $prevMonthSales = Sale::whereMonth('updated_at', $prevMonth->month)
            ->whereYear('updated_at', $prevMonth->year)
            ->where('sale_status', 'paid')
            ->sum('total_recieved');

        // Get current month's damage items
        $currentMonthDamages = DamageItem::whereMonth('reported_date', $month)
            ->whereYear('reported_date', $year)
            ->sum('total_cost');

        // Calculate total giving SC
        $totalGivingSC = max(0, $prevMonthSales - $currentMonthDamages);

        // Get all points
        $totalPoints = ServiceChargePoint::sum('points');
        $personPoints = ServiceChargePoint::where('person_id', $person->id)->first();

        if (!$personPoints || $totalPoints == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Points not assigned or total points is zero'
            ], 400);
        }

        // Calculate final amount
        $pointsRatio = $personPoints->points / $totalPoints;
        $finalAmount = $totalGivingSC * $pointsRatio;

        // Create service charge record
        $serviceCharge = ServiceCharge::create([
            'person_id' => $person->id,
            'month' => $month,
            'year' => $year,
            'total_sc' => $totalGivingSC,
            'points_ratio' => $pointsRatio,
            'final_amount' => $finalAmount,
            'remarks' => "Service Charge for " . Carbon::create()->month($month)->format('F') . " $year"
        ]);

        return response()->json([
            'success' => true,
            'serviceCharge' => $serviceCharge
        ]);
    }

    public function printServiceCharge($id)
    {
        $serviceCharge = ServiceCharge::with('person')->findOrFail($id);
        return view('service-charge.print', compact('serviceCharge'));
    }

    public function managePoints()
{
    $staff = DB::table('persons')
        ->join('staff_codes', 'persons.id', '=', 'staff_codes.person_id')
        ->where('staff_codes.is_active', 1)
        ->select('persons.id', 'persons.name', 'persons.created_at', 'persons.updated_at', 'persons.type')
        ->groupBy('persons.id', 'persons.name', 'persons.created_at', 'persons.updated_at', 'persons.type')
        ->orderBy('persons.name')
        ->get();

    $points = ServiceChargePoint::all()->keyBy('person_id');
    
    return view('service-charge.points', compact('staff', 'points'));
}

public function updatePointsBulk(Request $request)
{
    $points = $request->validate([
        'points' => 'required|array',
        'points.*' => 'required|integer|min:0'
    ]);

    foreach ($points['points'] as $personId => $pointValue) {
        ServiceChargePoint::updateOrCreate(
            ['person_id' => $personId],
            ['points' => $pointValue]
        );
    }

    return redirect()->back()->with('success', 'Points updated successfully');
}
}