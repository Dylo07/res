<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use App\Models\Group;
use App\Models\Person;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get filter parameters with defaults
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedDate = $request->get('date', Carbon::now()->format('Y-m-d'));

        // Parse dates for queries
        $monthYear = Carbon::parse($month);
        $startOfMonth = $monthYear->copy()->startOfMonth();
        $endOfMonth = $monthYear->copy()->endOfMonth();

        // Fetch monthly costs
        $monthlyCosts = Cost::with(['group', 'person', 'user'])
            ->whereYear('cost_date', $monthYear->year)
            ->whereMonth('cost_date', $monthYear->month)
            ->get();

        // Fetch daily costs
        $dailyCosts = Cost::with(['group', 'person', 'user'])
            ->whereDate('cost_date', $selectedDate)
            ->get();

        // Group monthly costs
        $monthlyGroupedCosts = $this->groupCostsByCategory($monthlyCosts);
        $dailyGroupedCosts = $this->groupCostsByCategory($dailyCosts);

        // Calculate grand total
        $grandTotal = $monthlyCosts->sum('amount');

        // Prepare chart data
        $chartData = $this->prepareChartData($monthlyCosts);

        // Calculate analytics
        $analytics = $this->calculateAnalytics($monthlyCosts, $dailyCosts);

        // Get log details for the selected date
        $logDetails = $dailyCosts->map(function ($cost) {
            return [
                'date' => Carbon::parse($cost->cost_date),
                'user' => $cost->user?->name ?? 'System',
                'category' => $cost->group->name,
                'person_shop' => $cost->person->name,
                'expense' => $cost->amount,
            ];
        });

        // Get groups and persons for filters
        $groups = Group::orderBy('name')->get();
        $persons = Person::orderBy('name')->get();


         // Add this section for the pie chart data

        $monthYear = Carbon::parse($month);
        $startOfMonth = $monthYear->copy()->startOfMonth();
        $endOfMonth = $monthYear->copy()->endOfMonth();
        
        // Get expense distribution
        $expenseDistribution = Cost::getMonthlyDistribution($startOfMonth, $endOfMonth);
        

        return view('costs.index', compact(
            'monthlyGroupedCosts',
            'dailyGroupedCosts',
            'logDetails',
            'month',
            'selectedDate',
            'groups',
            'persons',
            'analytics',
            'chartData',
            'grandTotal',
            'expenseDistribution' 
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Group::orderBy('name')->get();
        $persons = Person::orderBy('name')->get();
        
        return view('costs.create', compact('groups', 'persons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'person_id' => 'required|exists:persons,id',
            'amount' => 'required|numeric|min:0',
            'cost_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        Cost::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('costs.index')
            ->with('success', 'Expense added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cost $cost)
    {
        $groups = Group::orderBy('name')->get();
        $persons = Person::orderBy('name')->get();
        
        return view('costs.edit', compact('cost', 'groups', 'persons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cost $cost)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'person_id' => 'required|exists:persons,id',
            'amount' => 'required|numeric|min:0',
            'cost_date' => 'required|date',
        ]);

        $cost->update($validated);

        return redirect()
            ->route('costs.index')
            ->with('success', 'Expense updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cost $cost)
    {
        $cost->delete();

        return redirect()
            ->route('costs.index')
            ->with('success', 'Expense deleted successfully!');
    }

    /**
     * Group costs by category and person.
     */
    private function groupCostsByCategory($costs)
    {
        return $costs->groupBy('group.name')
            ->map(function ($groupCosts) {
                return $groupCosts->groupBy('person.name')
                    ->map(function ($personCosts) {
                        return [
                            'costs' => $personCosts->map(function ($cost) {
                                // Ensure cost_date is a Carbon instance
                                $cost->cost_date = Carbon::parse($cost->cost_date);
                                return $cost;
                            })->sortByDesc('cost_date'),
                            'total' => $personCosts->sum('amount')
                        ];
                    });
            });
    }

    /**
     * Calculate analytics for the dashboard.
     */
    private function calculateAnalytics($monthlyCosts, $dailyCosts)
{
    $monthlyTotal = $monthlyCosts->sum('amount');
    $dailyTotal = $dailyCosts->sum('amount');
    $transactionCount = $monthlyCosts->count();

    // Calculate category breakdown with proper sorting
    $categoryBreakdown = $monthlyCosts
        ->groupBy('group.name')
        ->map(function ($costs) {
            return [
                'name' => $costs->first()->group->name ?? 'N/A',
                'total' => $costs->sum('amount'),
                'count' => $costs->count()
            ];
        })
        ->sortByDesc(function($category) {
            return $category['total'];
        })
        ->values();

    // Calculate average transaction amount
    $avgTransaction = $transactionCount > 0 
        ? round($monthlyTotal / $transactionCount, 2) 
        : 0;

    // Previous month comparison
    $previousMonth = now()->subMonth();
    $previousMonthTotal = Cost::whereYear('cost_date', $previousMonth->year)
        ->whereMonth('cost_date', $previousMonth->month)
        ->sum('amount');

    $trend_percentage = $previousMonthTotal > 0 
        ? round((($monthlyTotal - $previousMonthTotal) / $previousMonthTotal) * 100, 1)
        : 0;

    // Get top category data safely
    $topCategory = $categoryBreakdown->first();

    return [
        'total_amount' => $monthlyTotal,
        'total_transactions' => $transactionCount,
        'avg_transaction' => $avgTransaction,
        'trend_percentage' => $trend_percentage,
        'category_breakdown' => $categoryBreakdown,
        'daily_total' => $dailyTotal,
        'top_category' => [
            'name' => $topCategory ? $topCategory['name'] : 'No Data',
            'total' => $topCategory ? $topCategory['total'] : 0,
            'count' => $topCategory ? $topCategory['count'] : 0
        ]
    ];
}
private function prepareChartData($costs)
{
    try {
        // Daily expenses chart data
        $dailyExpenses = $costs
            ->groupBy(function($cost) {
                return $cost->cost_date->format('Y-m-d');
            })
            ->map(function($dayCosts) {
                return [
                    'date' => $dayCosts->first()->cost_date->format('M d'),
                    'total' => $dayCosts->sum('amount')
                ];
            })
            ->values();

        // Category distribution chart data
        $categoryDistribution = $costs
            ->groupBy('group.name')
            ->map(function($groupCosts, $groupName) {
                return [
                    'category' => $groupName,
                    'total' => $groupCosts->sum('amount')
                ];
            })
            ->sortByDesc('total')
            ->values();

        return [
            'dailyExpenses' => $dailyExpenses,
            'categoryDistribution' => $categoryDistribution
        ];

    } catch (\Exception $e) {
        \Log::error('Error preparing chart data: ' . $e->getMessage());
        return [
            'dailyExpenses' => collect(),
            'categoryDistribution' => collect()
        ];
    }
}
    /**
     * Export expenses to CSV.
     */
    public function export(Request $request)
    {
        try {
            $month = $request->get('month', Carbon::now()->format('Y-m'));
            $monthYear = Carbon::parse($month);

            $costs = Cost::with(['group', 'person', 'user'])
                ->whereYear('cost_date', $monthYear->year)
                ->whereMonth('cost_date', $monthYear->month)
                ->orderBy('cost_date', 'desc')
                ->get();

            $filename = "expenses_{$month}.csv";
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$filename}",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $handle = fopen('php://temp', 'w');
            
            // Add headers
            fputcsv($handle, [
                'Date',
                'Category',
                'Person/Shop',
                'Amount',
                'Added By',
                'Created At'
            ]);

            // Add data rows
            foreach ($costs as $cost) {
                fputcsv($handle, [
                    $cost->cost_date->format('Y-m-d'),
                    $cost->group->name,
                    $cost->person->name,
                    number_format($cost->amount, 2),
                    $cost->user?->name ?? 'System',
                    $cost->created_at->format('Y-m-d H:i:s')
                ]);
            }

            // Reset file pointer
            rewind($handle);
            
            // Get content
            $content = stream_get_contents($handle);
            fclose($handle);

            return response($content, 200, $headers);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    public function show(Cost $cost)
    {
        return view('costs.show', compact('cost'));
    }


    public function printDailyExpenses(Request $request)
    {
        $selectedDate = $request->get('date', Carbon::now()->format('Y-m-d'));
        
        $dailyCosts = Cost::with(['group', 'person', 'user'])
            ->whereDate('cost_date', $selectedDate)
            ->get();
            
        $dailyGroupedCosts = $this->groupCostsByCategory($dailyCosts);

        return view('costs.print', compact('dailyGroupedCosts', 'selectedDate'));
    }

    public function printTransaction(Cost $cost)
{
    return view('costs.print-transaction', compact('cost'));
}
}