<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ProductGroup;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\StockLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Get current date parameters
        $currentMonth = $request->input('month', now()->month);
        $currentYear = $request->input('year', now()->year);
        $categoryId = $request->input('category_id');
        $selectedDate = $request->input('log_date', now()->toDateString());
        $usageDate = $request->input('usage_date', now()->toDateString());
        $usageCategory = $request->input('usage_category');
        
        // Create Carbon instances for date handling
        $currentDate = Carbon::createFromDate($currentYear, $currentMonth, 1);
        
        // Prevent future months from being accessed
        if ($currentDate->isAfter(now())) {
            return redirect()->route('stock.index', [
                'month' => now()->month,
                'year' => now()->year,
                'category_id' => $categoryId
            ])->with('error', 'Cannot view future months');
        }
        
        // Get previous month's last day for stock carryover
        $lastDayOfPreviousMonth = $currentDate->copy()->subDay();
        
        // Get all groups for the dropdown
        $groups = ProductGroup::all();
        
        // Initialize $selectedGroup as null
        $selectedGroup = null;
        
        // If category is selected, load its items with inventory
        if ($categoryId) {
            $selectedGroup = ProductGroup::with(['items' => function ($query) use ($currentDate, $lastDayOfPreviousMonth) {
                $query->with(['inventory' => function ($invQuery) use ($currentDate, $lastDayOfPreviousMonth) {
                    $invQuery->where(function ($q) use ($currentDate, $lastDayOfPreviousMonth) {
                        $q->whereYear('stock_date', $currentDate->year)
                          ->whereMonth('stock_date', $currentDate->month)
                          ->orWhere('stock_date', $lastDayOfPreviousMonth->toDateString());
                    })->orderBy('stock_date');
                }]);
            }])->find($categoryId);
            
            if ($selectedGroup) {
                $groups = $groups->map(function ($group) use ($selectedGroup) {
                    if ($group->id === $selectedGroup->id) {
                        return $selectedGroup;
                    }
                    return $group;
                });
            }
        }
        
        // Get ALL logs for the current month for stock calculations
        $monthLogs = StockLog::with(['user', 'item'])
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get paginated logs for the selected date for display
        $logs = StockLog::with(['user', 'item'])
            ->whereDate('created_at', $selectedDate)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'logs_page');

        // Get usage logs for category-wise report
        $usageLogsQuery = StockLog::with(['user', 'item'])
            ->whereDate('created_at', $usageDate);

        if ($usageCategory) {
            $usageLogsQuery->where('action', $usageCategory);
        } else {
            // Show only removal actions for usage report
            $usageLogsQuery->whereIn('action', [
                'remove_main_kitchen', 
                'remove_banquet_hall_kitchen', 
                'remove_banquet_hall', 
                'remove_restaurant', 
                'remove_rooms', 
                'remove_garden', 
                'remove_other'
            ]);
        }

        $usageLogs = $usageLogsQuery->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'usage_page');

        // Get usage summary for the selected date
        $usageSummary = StockLog::whereDate('created_at', $usageDate)
            ->whereIn('action', [
                'remove_main_kitchen', 
                'remove_banquet_hall_kitchen', 
                'remove_banquet_hall', 
                'remove_restaurant', 
                'remove_rooms', 
                'remove_garden', 
                'remove_other'
            ])
            ->selectRaw('action, COUNT(DISTINCT item_id) as item_count, SUM(quantity) as total_quantity')
            ->groupBy('action')
            ->get();

        return view('stock.index', compact(
            'groups', 
            'currentMonth', 
            'currentYear', 
            'logs', 
            'selectedGroup', 
            'monthLogs',
            'usageLogs',
            'usageSummary'
        ));
    }

    public function updateTodayStock(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'action' => 'required|in:add,remove_main_kitchen,remove_banquet_hall_kitchen,remove_banquet_hall,remove_restaurant,remove_rooms,remove_garden,remove_other',
        ]);

        $itemId = $request->item_id;
        $quantity = floatval($request->quantity);
        $description = $request->description;
        $action = $request->action;
        $today = now()->toDateString();

        try {
            DB::transaction(function () use ($itemId, $quantity, $description, $today, $action) {
                // Get today's inventory record or create new one
                $inventory = Inventory::firstOrNew(['item_id' => $itemId, 'stock_date' => $today]);

                if (!$inventory->exists) {
                    $previousInventory = Inventory::where('item_id', $itemId)
                        ->where('stock_date', '<', $today)
                        ->orderBy('stock_date', 'desc')
                        ->first();

                    $inventory->stock_level = $previousInventory ? $previousInventory->stock_level : 0;
                }

                // Calculate new stock level based on action
                $originalStockLevel = $inventory->stock_level;

                if ($action === 'add') {
                    $inventory->stock_level += $quantity;
                } else {
                    // All removal actions (category-specific) reduce stock
                    $inventory->stock_level = max(0, $inventory->stock_level - $quantity);
                }

                $inventory->save();

                // Only propagate if the stock level has changed
                if ($originalStockLevel !== $inventory->stock_level) {
                    $this->propagateStock($itemId, $today, $inventory->stock_level);
                }

                // Create stock log with the specific action type
                StockLog::create([
                    'item_id' => $itemId,
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'quantity' => $quantity,
                    'description' => $description,
                ]);
            });

            // Set success message based on action
            $actionName = $this->getActionDisplayName($action);
            $message = $action === 'add' 
                ? 'Stock added successfully.' 
                : "Stock removed for {$actionName} successfully.";

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Stock update error', [
                'error' => $e->getMessage(),
                'item_id' => $itemId,
                'action' => $action,
                'quantity' => $quantity,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'An error occurred while updating stock. Please try again.');
        }
    }

    private function propagateStock($itemId, $startDate, $stockLevel)
    {
        try {
            // Convert to Carbon instance for date manipulation
            $startDateCarbon = Carbon::parse($startDate);
            
            // Get the end of next 3 months as the maximum date for propagation
            $endOfNextMonth = Carbon::parse($startDate)->addMonths(3)->endOfMonth();
            
            // Delete any existing future records to prevent conflicts
            Inventory::where('item_id', $itemId)
                ->where('stock_date', '>', $startDate)
                ->where('stock_date', '<=', $endOfNextMonth)
                ->delete();
                
            // Propagate the current stock level to all future dates
            $currentDate = $startDateCarbon->copy()->addDay();
            
            $recordsToInsert = [];
            while ($currentDate->lte($endOfNextMonth)) {
                $recordsToInsert[] = [
                    'item_id' => $itemId,
                    'stock_date' => $currentDate->toDateString(),
                    'stock_level' => $stockLevel,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $currentDate->addDay();
                
                // Insert in batches of 100 to avoid memory issues
                if (count($recordsToInsert) >= 100) {
                    Inventory::insert($recordsToInsert);
                    $recordsToInsert = [];
                }
            }
            
            // Insert remaining records
            if (!empty($recordsToInsert)) {
                Inventory::insert($recordsToInsert);
            }

        } catch (\Exception $e) {
            \Log::error('Stock propagation error', [
                'error' => $e->getMessage(),
                'item_id' => $itemId,
                'start_date' => $startDate,
                'stock_level' => $stockLevel
            ]);
            throw $e;
        }
    }

    /**
     * Get display name for action
     */
    private function getActionDisplayName($action)
    {
        $actionNames = [
            'add' => 'Stock Addition',
            'remove_main_kitchen' => 'Main Kitchen',
            'remove_banquet_hall_kitchen' => 'Banquet Hall Kitchen',
            'remove_banquet_hall' => 'Banquet Hall',
            'remove_restaurant' => 'Restaurant',
            'remove_rooms' => 'Rooms',
            'remove_garden' => 'Garden',
            'remove_other' => 'Other',
        ];

        return $actionNames[$action] ?? ucfirst(str_replace('_', ' ', $action));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'stock_date' => 'required|date',
            'stock_level' => 'required|numeric|min:0.1',
        ]);

        $itemId = $request->item_id;
        $stockDate = $request->stock_date;
        $stockLevel = $request->stock_level;

        try {
            DB::transaction(function () use ($itemId, $stockDate, $stockLevel) {
                // Update or create stock for the given date
                Inventory::updateOrCreate(
                    ['item_id' => $itemId, 'stock_date' => $stockDate],
                    ['stock_level' => $stockLevel]
                );

                // Propagate stock level to future days
                $this->propagateStock($itemId, $stockDate, $stockLevel);
            });

            return redirect()->back()->with('success', 'Stock updated and propagated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating stock: ' . $e->getMessage());
        }
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_groups,name',
        ]);

        try {
            ProductGroup::create([
                'name' => $request->name,
            ]);

            return redirect()->back()->with('success', 'Category added successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error adding category: ' . $e->getMessage());
        }
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'group_id' => 'required|exists:product_groups,id',
        ]);

        try {
            Item::create([
                'name' => $request->name,
                'group_id' => $request->group_id,
            ]);

            return redirect()->back()->with('success', 'Item added successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error adding item: ' . $e->getMessage());
        }
    }

    public function viewMonthlyStock(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:' . now()->year,
        ]);

        $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);
        $year = $request->year;

        $groups = ProductGroup::with(['items' => function ($query) use ($month, $year) {
            $query->with(['inventory' => function ($inventoryQuery) use ($month, $year) {
                $inventoryQuery->whereYear('stock_date', $year)
                    ->whereMonth('stock_date', $month)
                    ->orderBy('stock_date');
            }]);
        }])->get();

        return view('stock.monthly', compact('groups', 'month', 'year'));
    }

    public function checkStockPropagation(Request $request)
    {
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate = $request->input('end_date', now()->addDays(7)->toDateString());
        $groups = ProductGroup::with('items')->get();

        if ($request->has('item_id')) {
            $request->validate([
                'item_id' => 'required|exists:items,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $item = Item::findOrFail($request->item_id);
            
            // Get the last known stock level before the start date
            $lastKnownStock = Inventory::where('item_id', $request->item_id)
                ->where('stock_date', '<=', $request->start_date)
                ->orderBy('stock_date', 'desc')
                ->first();

            $stockLevel = $lastKnownStock ? $lastKnownStock->stock_level : 0;
            
            // Generate date range with this stock level
            $stockLevels = collect();
            $currentDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            
            while ($currentDate <= $endDate) {
                $dateRecord = Inventory::where('item_id', $request->item_id)
                    ->where('stock_date', $currentDate->toDateString())
                    ->first();
                    
                $stockLevels->push([
                    'date' => $currentDate->toDateString(),
                    'stock_level' => $dateRecord ? $dateRecord->stock_level : $stockLevel
                ]);
                
                $currentDate->addDay();
            }

            return view('stock.propagation-test', compact('stockLevels', 'item', 'groups', 'startDate', 'endDate'));
        }

        return view('stock.propagation-test', compact('groups', 'startDate', 'endDate'));
    }

    public function categoriesProducts()
    {
        $groups = ProductGroup::withCount('items')->with('items')->get();
        return view('stock.categories-products', compact('groups'));
    }

    public function getUsageReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $category = $request->input('category');

        try {
            $query = StockLog::with(['user', 'item'])
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereIn('action', [
                    'remove_main_kitchen', 
                    'remove_banquet_hall_kitchen', 
                    'remove_banquet_hall', 
                    'remove_restaurant', 
                    'remove_rooms', 
                    'remove_garden', 
                    'remove_other'
                ]);

            if ($category) {
                $query->where('action', $category);
            }

            $usageData = $query->orderBy('created_at', 'desc')->get();

            // Group by action for summary
            $summary = $usageData->groupBy('action')->map(function ($items, $action) {
                return [
                    'action' => $action,
                    'action_name' => $this->getActionDisplayName($action),
                    'total_quantity' => $items->sum('quantity'),
                    'unique_items' => $items->unique('item_id')->count(),
                    'transactions' => $items->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'usage_data' => $usageData,
                'summary' => $summary,
                'total_quantity' => $usageData->sum('quantity'),
                'total_transactions' => $usageData->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching usage report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportUsageReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $format = $request->input('format', 'csv');

        try {
            $usageData = StockLog::with(['user', 'item', 'item.group'])
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereIn('action', [
                    'remove_main_kitchen', 
                    'remove_banquet_hall_kitchen', 
                    'remove_banquet_hall', 
                    'remove_restaurant', 
                    'remove_rooms', 
                    'remove_garden', 
                    'remove_other'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Prepare data for export
            $exportData = $usageData->map(function ($log) {
                return [
                    'Date' => $log->created_at->format('Y-m-d'),
                    'Time' => $log->created_at->format('H:i:s'),
                    'User' => $log->user->name,
                    'Category' => $log->item->group->name,
                    'Item' => $log->item->name,
                    'Location' => $this->getActionDisplayName($log->action),
                    'Quantity' => $log->quantity,
                    'Description' => $log->description,
                ];
            });

            if ($format === 'csv') {
                $fileName = "usage_report_{$startDate}_to_{$endDate}.csv";
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"$fileName\"",
                ];

                $callback = function () use ($exportData) {
                    $file = fopen('php://output', 'w');
                    
                    // Add headers
                    if ($exportData->isNotEmpty()) {
                        fputcsv($file, array_keys($exportData->first()));
                    }
                    
                    // Add data
                    foreach ($exportData as $row) {
                        fputcsv($file, $row);
                    }
                    
                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            }

            return response()->json(['error' => 'Only CSV format is currently supported'], 400);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }
}