<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\InStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function index() {
        $categories = Category::all();
        return view('cashier.index')->with('categories', $categories);
    }

    public function getTables(){
        $tables = Table::all();
        $html = '';
        
        foreach($tables as $table){
            $html .= '<div class="col-lg-2 col-md-3 col-sm-1 mb-2">';
            $html .= '<button tabindex="-1" class="btn btn-dark btn-outline-secondary btn-table" data-id="'.$table->id.'" data-name="'.$table->name.'" >';
            if($table->status == "available"){
                $html .= '<span class="badge badge-pill badge-success">'.$table->name.'</span>';
            }else{
                $html .= '<span class="badge badge-pill badge-danger">'.$table->name.'</span>';
            }
            $html .='</button>';
            $html .= '</div>';
        }
        return $html;
    }
    
    public function getMenuByCategory($category_id,$search_keyword = ''){
        if($category_id == 0){
            $menus = Menu::where('name', 'LIKE', "%{$search_keyword}%")->get();
        }else{
            $menus = Menu::where('category_id', $category_id)->get();
        }
        $html = '';
        foreach($menus as $menu){
            $html .= '
            <div class="col-md-auto  ml-1  mt-3 " >
                <a class="btn  btn-outline-success btn-light  btn-menu  "  data-id="'.$menu->id.'">
                    <br>
                    '.$menu->name.'
                    <br>
                    Rs'.number_format($menu->price).'
                </a>
            </div>';
        }
        return $html;
    }

    public function orderFood(Request $request){
        $menu = Menu::find($request->menu_id);
        $table_id = $request->table_id;
        $table_name = $request->table_name;
        $sale = Sale::where('table_id', $table_id)->where('sale_status','unpaid')->first();
        
        $tableStatusChanged = false;
        
        if(!$sale){
            $user = Auth::user();
            $sale = new Sale();
            $sale->table_id = $table_id;
            $sale->table_name = $table_name;
            $sale->user_id = $user->id;
            $sale->user_name = $user->name;
            $sale->total_price = 0; // Initialize to 0
            $sale->save();
            $sale_id = $sale->id;
            
            $table = Table::find($table_id);
            $table->status = "unavailable";
            $table->save();
            $tableStatusChanged = true;
        }else{
            $sale_id = $sale->id;
        }

        $saleDetail = new SaleDetail();
        $saleDetail->sale_id = $sale_id;
        $saleDetail->menu_id = $menu->id;
        $saleDetail->menu_name = $menu->name;
        $saleDetail->menu_price = $menu->price;
        $saleDetail->quantity = $request->quantity;
        $saleDetail->count = 1;
        $saleDetail->save();

        // Recalculate total properly
        $this->recalculateSaleTotal($sale_id);

        return [
            'html' => $this->getSaleDetails($sale_id),
            'tableStatusChanged' => $tableStatusChanged,
            'tableId' => $table_id
        ];
    }

    public function getSaleDetailsByTable($table_id){
        $sale = Sale::where('table_id', $table_id)->where('sale_status','unpaid')->first();
        $html = '';
        if($sale){
            $sale_id = $sale->id;
            $html .= $this->getSaleDetails($sale_id);
        }else{
            $html .= "Not Found Any Sale Details for the Selected Table";
        }
        return $html;
    }

    private function getSaleDetails($sale_id){
        $html = '<p>Sale ID: '.$sale_id.'</p>';
        $saleDetails = SaleDetail::where('sale_id', $sale_id)->get();
        $html .= '<div class="table-responsive-md" tabindex ="-1" style="overflow-y:scroll; min-height: 400px; border: 1px solid #343A40">
        <table class="table table-stripped table-dark">
        <thead>
            <tr>
                <th scope="col">Menu</th>
                <th scope="col">Quantity</th>
                <th scope="col">Price</th>
                <th scope="col">Total</th>
                <th scope="col">Updated Time</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>';
        
        $showBtnPayment = true;
        $totalItemsAmount = 0; // Track actual items total
        
        foreach($saleDetails as $saleDetail){
            $itemTotal = $saleDetail->menu_price * $saleDetail->quantity;
            $totalItemsAmount += $itemTotal; // Add to running total
            
            $updatedDateTime = $saleDetail->updated_at ? $saleDetail->updated_at->format('d/m/Y H:i:s') : '';
            $html .= '
            <tr>
                <td>'.$saleDetail->menu_name.'</td>
                <td><input type="number" tabindex ="-1" class="change-quantity" data-id="'.$saleDetail->id.'" 
                           style="width:50px;" value="'.$saleDetail->quantity.'"'.
                           ($saleDetail->status == "confirm" ? ' disabled' : '').'></td>
                <td>'.$saleDetail->menu_price.'</td>
                <td>'.number_format($itemTotal, 2).'</td>
                <td>'.$updatedDateTime.'</td>';
                if($saleDetail->status == "noConfirm"){
                    $showBtnPayment = false;
                    $html .= '<td><a data-id="'.$saleDetail->id.'" class="btn btn-danger btn-delete-saledetail"><i class="far fa-trash-alt"></a></td>';
                }else{
                    $html .= '<td><i class="fas fa-check-circle"></i></td>';
                }
            $html .= '</tr>';
        }
        $html .='</tbody></table></div>';

        // Get sale record 
        $sale = Sale::find($sale_id);
        
        // Ensure sale total_price matches the sum of all items
        if ($sale && abs($sale->total_price - $totalItemsAmount) > 0.01) {
            $sale->total_price = $totalItemsAmount;
            $sale->save();
        }

        $html .= '<hr>';
        $html .= '<h3>Total Amount: Rs '.number_format($sale->total_price, 2).'</h3>';

        if($showBtnPayment){
            $html .= '<button data-id="'.$sale_id.'" data-totalAmount="'.$sale->total_price.'" class="btn btn-success btn-block btn-payment" data-toggle="modal" data-target="#exampleModal">Payment</button>';
            $html .= '<button data-id="'.$sale_id.'" class="btn btn-dark btn-block btn-payment printKot">Print KOT</button>';
        }else{
            $html .= '<button data-id="'.$sale_id.'" class="btn btn-warning btn-block btn-confirm-order">Confirm Order</button>';
        }

        return $html;
    }

    public function increaseQuantity(Request $request){
        $saleDetail_id = $request->saleDetail_id;
        $saleDetail = SaleDetail::where('id',$saleDetail_id)->first();
        $saleDetail->quantity = $saleDetail->quantity + 1;
        $saleDetail->save();
        
        // Recalculate total properly
        $this->recalculateSaleTotal($saleDetail->sale_id);
        
        return $this->getSaleDetails($saleDetail->sale_id);
    }

    public function changesQuantity(Request $request){
        $saleDetail_id = $request->saleDetail_id;
        $qty = $request->qty;
        $saleDetail = SaleDetail::where('id',$saleDetail_id)->first();

        // Update quantity
        $saleDetail->quantity = $qty;
        $saleDetail->save();
        
        // Recalculate total properly
        $this->recalculateSaleTotal($saleDetail->sale_id);
        
        return $this->getSaleDetails($saleDetail->sale_id);
    }

    public function decreaseQuantity(Request $request){
        $saleDetail_id = $request->saleDetail_id;
        $saleDetail = SaleDetail::where('id',$saleDetail_id)->first();
        
        if($saleDetail->quantity > 1) {
            $saleDetail->quantity = $saleDetail->quantity - 1;
            $saleDetail->save();
        }
        
        // Recalculate total properly
        $this->recalculateSaleTotal($saleDetail->sale_id);
        
        return $this->getSaleDetails($saleDetail->sale_id);
    }

    public function confirmOrderStatus(Request $request) {
        $sale_id = $request->sale_id;
        
        // Get existing quantities before update
        $saleDetails = SaleDetail::where('sale_id', $sale_id)->get();
        
        // Update status first
        SaleDetail::where('sale_id', $sale_id)->update(['status' => 'confirm']);
        
        // Update count while preserving quantity
        foreach ($saleDetails as $detail) {
            SaleDetail::where('id', $detail->id)->update([
                'count' => $detail->count + 1,
                'quantity' => $detail->quantity  // Explicitly preserve quantity
            ]);
        }
        
        return $this->getSaleDetails($sale_id);
    }

    public function deleteSaleDetail(Request $request){
        $saleDetail_id = $request->saleDetail_id;
        $saleDetail = SaleDetail::find($saleDetail_id);
        $sale_id = $saleDetail->sale_id;
        
        // Delete the item
        $saleDetail->delete();

        // Recalculate total properly
        $this->recalculateSaleTotal($sale_id);
        
        $saleDetails = SaleDetail::where('sale_id', $sale_id)->first();
        if($saleDetails){
            $html = $this->getSaleDetails($sale_id);
        }else{
            $html = "Not Found Any Sale Details for the Selected Table";
        }
        return $html;
    }

    // NEW METHOD: Properly recalculate sale totals
    private function recalculateSaleTotal($sale_id) {
        try {
            // Get all sale details for this sale
            $saleDetails = SaleDetail::where('sale_id', $sale_id)->get();
            
            // Calculate correct total
            $correctTotal = $saleDetails->sum(function($detail) {
                return $detail->menu_price * $detail->quantity;
            });
            
            // Update the sale record
            $sale = Sale::find($sale_id);
            if ($sale) {
                $sale->total_price = $correctTotal;
                $sale->save();
            }
            
            return $correctTotal;
        } catch (\Exception $e) {
            \Log::error('Error recalculating sale total: ' . $e->getMessage(), [
                'sale_id' => $sale_id
            ]);
            return 0;
        }
    }

    public function savePayment(Request $request){
        $saleID = $request->saleID;
        $recievedAmount = $request->recievedAmount;
        $paymentType = $request->PaymentType;
        
        // Begin transaction for data consistency
        DB::beginTransaction();
        
        try {
            $sale = Sale::find($saleID);
            $sale->total_recieved = $recievedAmount;
            $sale->change = $recievedAmount + $sale->total_price;
            $sale->payment_type = $paymentType;
            $sale->sale_status = "paid";
            $sale->save();
            
            $table = Table::find($sale->table_id);
            $table->status = "available";
            $table->save();
            
            // Only reduce stock if it hasn't been reduced yet
            if (!$this->hasStockBeenReduced($saleID)) {
                $saleDetail = SaleDetail::where('sale_id', $saleID)->get();
                
                foreach ($saleDetail as $value) {
                    $user = Auth::user();
                    $stock = new InStock();
                    $stock->menu_id = $value->menu_id;
                    $stock->stock = -intval($value->quantity);
                    $stock->user_id = $user->id;
                    $stock->save();
        
                    $menu = Menu::find($value->menu_id);
                    $menu->stock = intval($menu->stock) - ($value->quantity);
                    $menu->save();     
                }
            }
            
            DB::commit();
            return url('/cashier/showRecipt')."/".$saleID;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error in savePayment: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing payment.'], 500);
        }
    }

    /**
     * Check if stock has already been reduced for this sale
     * 
     * @param int $saleId
     * @return bool
     */
    private function hasStockBeenReduced($saleId)
    {
        try {
            // Get sale details to check against recent stock reductions
            $sale = Sale::find($saleId);
            if (!$sale) {
                return false;
            }
            
            $saleDetails = SaleDetail::where('sale_id', $saleId)->get();
            $menuIds = $saleDetails->pluck('menu_id')->toArray();
            
            if (empty($menuIds)) {
                return false;
            }
            
            // Look for recent stock reductions for these menu items by the same user
            $recentReductions = InStock::whereIn('menu_id', $menuIds)
                ->where('user_id', $sale->user_id)
                ->where('stock', '<', 0)
                ->where('created_at', '>=', $sale->created_at->subMinutes(5)) // Within 5 minutes of sale
                ->where('created_at', '<=', $sale->updated_at->addMinutes(5))
                ->count();
            
            return $recentReductions >= count($menuIds);
            
        } catch (\Exception $e) {
            \Log::warning('Error checking stock reduction status: ' . $e->getMessage(), [
                'sale_id' => $saleId
            ]);
            // If we can't determine, assume stock hasn't been reduced to be safe
            return false;
        }
    }

    public function showRecipt($saleID){
        $sale = Sale::find($saleID);
        $saleDetails = SaleDetail::where('sale_id', $saleID)->get();
        return view('cashier.showRecipt')->with('sale',$sale)->with('saleDetails', $saleDetails);
    }

    public function printOrder(Request $request){
        return url('/cashier/printOrderRec')."/".$request->saleID;
    }

    public function printOrderRec($saleID){
        $sale = Sale::find($saleID);
        $saleDetails = SaleDetail::get()->where('sale_id',$saleID)->where('count',2);
        return view('cashier.printOrder')->with('sale',$sale)->with('saleDetails', $saleDetails);
    }
}