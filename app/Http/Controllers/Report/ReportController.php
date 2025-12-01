<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Category;

use App\Exports\SaleReportExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class ReportController extends Controller
{
    //
    public function index(){
        return view('report.index');
    }
    public function show(Request $request){
        $request->validate([
            'dateStart' => 'required',
            'dateEnd' => 'required'
        ]);
        $dateStart = date("Y-m-d H:i:s", strtotime($request->dateStart.' 00:00:00'));
        $dateEnd = date("Y-m-d H:i:s", strtotime($request->dateEnd.' 23:59:59'));

        $sales = Sale::whereBetween('updated_at', [$dateStart, $dateEnd])->where('sale_status','paid');
      
        $summarySales = Sale::select('menu_id','menu_name','categories.name', DB::raw('SUM(sale_details.quantity) as qty_sum'))
        ->join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
        ->join('menus', 'menus.id', '=', 'sale_details.menu_id')
        ->join('categories', 'categories.id', '=', 'menus.category_id')
        
        ->whereBetween('sales.updated_at', [$dateStart, $dateEnd])
        ->where('sales.sale_status','paid')
        ->where('sale_details.quantity','>','0')

        ->groupBy('sale_details.menu_id','menu_name','categories.name')
        ->orderby('categories.name','asc')
        ->get();


        return view('report.showReport')->with('dateStart', date("m/d/Y H:i:s", strtotime($request->dateStart.' 00:00:00')))
        ->with('dateEnd', date("m/d/Y H:i:s", strtotime($request->dateEnd.' 23:59:59')))
        ->with('totalSale', $sales->sum('change'))
        ->with('serviceCharge', $sales->sum('total_recieved'))
        ->with('summarySales', $summarySales)
        ->with('sales', $sales->paginate(500));

    }
    public function export(Request $request){
        return Excel::download(new SaleReportExport($request->dateStart, $request->dateEnd), 'saleReport.xlsx');
    }
}
