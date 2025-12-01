<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\RoomBooking;
use App\Models\VehicleSecurity;
use App\Models\Cost; 
use App\Models\Task;
use Carbon\Carbon;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    
     public function index(Request $request)
     {
         // Get selected month or default to current month
         $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
         $today = Carbon::now();
     
         // Calculate regular month dates for service charge
         $dateStart = Carbon::parse($selectedMonth)->startOfMonth();
         $dateEnd = Carbon::parse($selectedMonth)->endOfMonth();
     
         // If it's current month, set end date to now
         if ($selectedMonth === Carbon::now()->format('Y-m')) {
             $dateEnd = Carbon::now();
         }
     
         // Get current month service charge
         $currentSales = Sale::whereBetween('updated_at', [$dateStart, $dateEnd])
                         ->where('sale_status', 'paid');
         $serviceCharge = $currentSales->sum('total_recieved');
     
         // Get previous month service charge
         $prevMonthStart = $dateStart->copy()->subMonth()->startOfMonth();
         $prevMonthEnd = $dateStart->copy()->subMonth()->endOfMonth();
         
         $previousSales = Sale::whereBetween('updated_at', [$prevMonthStart, $prevMonthEnd])
                         ->where('sale_status', 'paid');
         $previousServiceCharge = $previousSales->sum('total_recieved');
     
         // Calculate percentage change
         $percentageChange = 0;
         if ($previousServiceCharge > 0) {
             $percentageChange = (($serviceCharge - $previousServiceCharge) / $previousServiceCharge) * 100;
         }
     
         // Generate months for dropdown (last 12 months)
         $months = [];
         for ($i = 0; $i < 12; $i++) {
             $date = Carbon::now()->subMonths($i);
             $months[$date->format('Y-m')] = $date->format('F Y');
         }
     
         $selectedDate = $request->get('date', date('Y-m-d'));
     
         // Get booked rooms
         $bookedRooms = RoomBooking::with('room')
             ->whereDate('guest_in_time', '<=', $selectedDate)
             ->where(function($query) use ($selectedDate) {
                 $query->whereNull('guest_out_time')
                       ->orWhereDate('guest_out_time', '>=', $selectedDate);
             })
             ->get();


             // Add this new query for vehicle room check-ins
    $selectedDate = $request->get('date', date('Y-m-d'));
    $roomVehicles = VehicleSecurity::whereNotNull('room_numbers')
    ->where('is_note', false)
    ->whereRaw("JSON_LENGTH(room_numbers) > 0")
    ->where(function($query) use ($selectedDate) {
        $query->whereDate('created_at', $selectedDate)  // Check-ins on selected date
              ->orWhereDate('checkout_time', $selectedDate)  // Check-outs on selected date
              ->orWhere(function($q) {
                  $q->whereNull('checkout_time')  // Not checked out yet
                    ->whereRaw("JSON_LENGTH(room_numbers) > 0");
              });
    })
    ->latest()
    ->get();
     
         // Get pending tasks
         $pendingTasks = Task::with('taskCategory')
             ->where('is_done', false)
             ->get();
     
         // Add period navigation for salary advances
         $selectedPeriod = $request->get('period', 0);
         $currentDay = $today->day;
         
         // Calculate periods for salary advances
         $periods = [];
         for ($i = 0; $i < 3; $i++) {
             $basePeriod = Carbon::now()->subMonths($i);
             
             if ($basePeriod->day > 10) {
                 $periodStart = Carbon::create($basePeriod->year, $basePeriod->month, 10, 0, 0, 0);
                 $periodEnd = Carbon::create($basePeriod->year, $basePeriod->month + 1, 10, 23, 59, 59);
             } else {
                 $periodStart = Carbon::create($basePeriod->year, $basePeriod->month - 1, 10, 0, 0, 0);
                 $periodEnd = Carbon::create($basePeriod->year, $basePeriod->month, 10, 23, 59, 59);
             }
     
             $periods[] = [
                 'start' => $periodStart,
                 'end' => $periodEnd,
                 'label' => $periodStart->format('M d, Y') . ' - ' . $periodEnd->format('M d, Y')
             ];
         }
     
         // Get selected period dates
         $selectedPeriodDates = $periods[$selectedPeriod];
         $startDate = $selectedPeriodDates['start'];
         $endDate = $selectedPeriodDates['end'];
     
         // Fetch salary advances for the selected period
         $salaryAdvances = Cost::with(['person', 'user'])
             ->where('group_id', 1)
             ->whereBetween('cost_date', [$startDate, $endDate])
             ->orderBy('cost_date', 'desc')
             ->get();
     
         $totalAdvance = $salaryAdvances->sum('amount');
         $dateRangeText = $selectedPeriodDates['label'];
     
         // Service charge period labels
         $periodLabel = $dateStart->format('M d, Y') . ' - ' . $dateEnd->format('M d, Y');
         $previousPeriodLabel = $prevMonthStart->format('M d, Y') . ' - ' . $prevMonthEnd->format('M d, Y');
     
         return view('home', compact(
             'serviceCharge',
             'previousServiceCharge',
             'percentageChange',
             'periodLabel',
             'previousPeriodLabel',
             'selectedMonth',
             'months',
             'roomVehicles', 
             'bookedRooms',
             'pendingTasks',
             'salaryAdvances',
             'totalAdvance',
             'dateRangeText',
             'selectedPeriod',
             'periods'
         ));
     }
}
