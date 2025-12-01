<?php

namespace App\Http\Controllers\inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InStock;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get selected date from request, default to today
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        
        // Validate date format
        try {
            $dateObject = Carbon::parse($selectedDate);
        } catch (\Exception $e) {
            $dateObject = Carbon::today();
            $selectedDate = $dateObject->format('Y-m-d');
        }

        // Get only categories with IDs 17 and 21 (your existing filter)
        $categories = Category::whereIn('id', [17, 21])->get();
        
        // Get menus grouped by category (your existing logic)
        $menus = Menu::whereIn('category_id', [17, 21])
                     ->with('category')
                     ->get()
                     ->groupBy('category_id');
        
        // Get daily sales data for the selected date
        $dailySales = $this->getDailySalesData($selectedDate);
        
        // Get grouped products for merge functionality
        $groupedProducts = $this->getGroupedProducts();
        
        // Get merged groups
        $mergedGroups = $this->getMergedGroups();
        
        // Prepare data array with all required information
        $data = array(
            'menus' => $menus,
            'categories' => $categories,
            'selectedDate' => $selectedDate,
            'dailySales' => $dailySales,
            'groupedProducts' => $groupedProducts,
            'mergedGroups' => $mergedGroups
        );
        
        return view('inventory.stock')->with('data', $data);
    }

    /**
     * Get daily sales data for a specific date
     * FIXED: Handles missing menu relationship gracefully
     * 
     * @param string $date Date in Y-m-d format
     * @return array Sales data grouped by category
     */
    private function getDailySalesData($date)
    {
        try {
            $startDate = Carbon::parse($date)->startOfDay();
            $endDate = Carbon::parse($date)->endOfDay();

            // Get all sales for the date that are paid
            $sales = Sale::whereBetween('updated_at', [$startDate, $endDate])
                ->where('sale_status', 'paid')
                ->get();

            // Initialize result structure
            $result = [
                'by_category' => [],
                'total_items' => 0
            ];

            // Process each sale
            foreach ($sales as $sale) {
                // Get sale details separately
                $saleDetails = SaleDetail::where('sale_id', $sale->id)->get();
                
                foreach ($saleDetails as $detail) {
                    // Get menu item by menu_id from the detail
                    $menu = Menu::with('category')->find($detail->menu_id);
                    
                    // Skip if menu or category not found
                    if (!$menu || !$menu->category) {
                        continue;
                    }

                    $categoryId = $menu->category_id;
                    $categoryName = $menu->category->name;

                    // Initialize category if not exists
                    if (!isset($result['by_category'][$categoryId])) {
                        $result['by_category'][$categoryId] = [
                            'name' => $categoryName,
                            'items' => [],
                            'total' => 0
                        ];
                    }

                    // Add item to category
                    $result['by_category'][$categoryId]['items'][] = [
                        'name' => $detail->menu_name ?? $menu->name,
                        'quantity' => $detail->quantity,
                        'user' => $sale->user_name ?? 'Unknown'
                    ];

                    $result['by_category'][$categoryId]['total'] += $detail->quantity;
                    $result['total_items'] += $detail->quantity;
                }
            }

            return $result;

        } catch (\Exception $e) {
            \Log::error('Error getting daily sales data: ' . $e->getMessage(), [
                'date' => $date,
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return [
                'by_category' => [],
                'total_items' => 0
            ];
        }
    }

    /**
     * Get grouped products for category 29 (liquor)
     * If you don't have category 29, this returns empty
     */
    private function getGroupedProducts()
    {
        try {
            $menus = Menu::where('category_id', 29)->get();
            $grouped = [];

            foreach ($menus as $menu) {
                // Extract base name without ml size
                $baseName = preg_replace('/\(\d+\s*ml\)/i', '', $menu->name);
                $baseName = trim($baseName);

                if (!isset($grouped[$baseName])) {
                    $grouped[$baseName] = [];
                }

                $grouped[$baseName][] = $menu;
            }

            // Only return groups with more than one item
            return array_filter($grouped, function($products) {
                return count($products) > 1;
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get merged product groups
     * Returns empty if merge functionality doesn't exist
     */
    private function getMergedGroups()
    {
        try {
            // Check if the merge columns exist in the database
            $columns = DB::getSchemaBuilder()->getColumnListing('menus');
            if (!in_array('is_merge_parent', $columns) || !in_array('merge_parent_id', $columns)) {
                return [];
            }

            $parentMenus = Menu::where('is_merge_parent', true)
                ->orWhereNotNull('merge_parent_id')
                ->get();

            $groups = [];
            foreach ($parentMenus as $menu) {
                if ($menu->is_merge_parent) {
                    $groups[$menu->id] = [
                        'parent' => $menu,
                        'children' => Menu::where('merge_parent_id', $menu->id)->get()
                    ];
                }
            }

            return $groups;
        } catch (\Exception $e) {
            // If merge functionality doesn't exist, return empty
            return [];
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * FIXED: Accepts itemid from route parameter (URL)
     *
     * @param  int  $itemid  The menu ID from the route
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $itemid = null)
    {
        try {
            // Get itemid from route parameter or request
            if ($itemid === null) {
                $itemid = $request->itemid ?? $request->route('itemid');
            }

            // Validate input
            $validated = $request->validate([
                'stock' => 'required|integer|min:1'
            ]);

            // Validate itemid exists
            if (!$itemid || !Menu::find($itemid)) {
                throw new \Exception('Invalid menu item');
            }

            DB::beginTransaction();

            $user = Auth::user();
            $stock = new InStock();
            $stock->menu_id = $itemid;
            $stock->stock = $request->stock;
            $stock->user_id = $user->id;
            $stock->save();

            $menu = Menu::find($itemid);
            $menu->stock = intval($menu->stock) + ($request->stock);
            $menu->save();

            DB::commit();

            $request->session()->flash('status', 'Stock saved successfully');
            return redirect('/inventory/stock');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error adding stock: ' . json_encode($e->errors()), [
                'item_id' => $itemid ?? 'unknown',
                'stock' => $request->stock ?? 'unknown'
            ]);
            
            $request->session()->flash('error', 'Invalid input. Please enter a valid quantity.');
            return redirect('/inventory/stock');
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error adding stock: ' . $e->getMessage(), [
                'item_id' => $itemid ?? 'unknown',
                'quantity' => $request->stock ?? 'unknown',
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            $request->session()->flash('error', 'Failed to add stock. Please try again.');
            return redirect('/inventory/stock');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $menu = Menu::with(['category', 'inStock.user'])->findOrFail($id);

            // Check if this is a merged parent (if merge functionality exists)
            try {
                $columns = DB::getSchemaBuilder()->getColumnListing('menus');
                if (in_array('is_merge_parent', $columns) && 
                    in_array('merge_parent_id', $columns) && 
                    isset($menu->is_merge_parent) && 
                    $menu->is_merge_parent) {
                    
                    // Load merged children
                    $menu->load(['mergedChildren.inStock.user']);
                    
                    // Combine all stock history
                    $allStocks = $menu->inStock;
                    foreach ($menu->mergedChildren as $child) {
                        $allStocks = $allStocks->merge($child->inStock);
                    }
                    $menu->all_in_stock = $allStocks->sortByDesc('created_at');
                }
            } catch (\Exception $e) {
                // Merge functionality doesn't exist, continue normally
            }

            return view('inventory.stockDetail')->with('menu', $menu);

        } catch (\Exception $e) {
            \Log::error('Error viewing stock detail: ' . $e->getMessage(), [
                'menu_id' => $id
            ]);

            return redirect('/inventory/stock')
                ->with('error', 'Stock item not found.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * FIXED: Accepts itemid from route parameter (URL)
     *
     * @param  int  $id  The menu ID from the route
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        try {
            // Get itemid from route parameter
            $itemid = $id;

            // Validate input
            $validated = $request->validate([
                'stock' => 'required|integer|min:1'
            ]);

            DB::beginTransaction();

            $menu = Menu::findOrFail($itemid);

            // Check if sufficient stock exists
            if ($menu->stock < $request->stock) {
                $request->session()->flash('warning', 'Insufficient stock! Available: ' . $menu->stock);
                return redirect('/inventory/stock');
            }

            $user = Auth::user();
            $stock = new InStock();
            $stock->menu_id = $itemid;
            $stock->stock = -intval($request->stock);
            $stock->user_id = $user->id;
            $stock->save();

            $menu->stock = intval($menu->stock) - ($request->stock);
            $menu->save();

            DB::commit();

            $request->session()->flash('warning', 'Stock has been removed successfully');
            return redirect('/inventory/stock');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error removing stock: ' . json_encode($e->errors()), [
                'item_id' => $id ?? 'unknown',
                'stock' => $request->stock ?? 'unknown'
            ]);
            
            $request->session()->flash('error', 'Invalid input. Please enter a valid quantity.');
            return redirect('/inventory/stock');
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error removing stock: ' . $e->getMessage(), [
                'item_id' => $id ?? 'unknown',
                'quantity' => $request->stock ?? 'unknown',
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            $request->session()->flash('error', 'Failed to remove stock. Please try again.');
            return redirect('/inventory/stock');
        }
    }

    /**
     * AJAX endpoint for daily sales data
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dailySales(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        
        $dailySales = $this->getDailySalesData($date);
        
        return response()->json($dailySales);
    }
}