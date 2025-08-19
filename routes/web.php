<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PettycashController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvInventoryController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\CompletedTaskController;
use App\Http\Controllers\RoomAvailabilityController;
use App\Http\Controllers\StaffAttendanceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\VehicleSecurityController;
use App\Http\Controllers\ManualAttendanceController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PackageCategoryController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\DamageItemController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ServiceChargeController;
use App\Http\Controllers\CashierBalanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Auth::routes(['register' =>false, 'reset' =>false ]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function(){
    
     //routes for cashier
     Route::get('/cashier', 'App\Http\Controllers\Cashier\CashierController@index')->name('cashier');
     Route::get('/cashier/getMenuByCategory/{category_id}/{search_keyword?}', 'App\Http\Controllers\Cashier\CashierController@getMenuByCategory');
     
     
     Route::get('/cashier/getTable', 'App\Http\Controllers\Cashier\CashierController@getTables');
     Route::get('/cashier/getSaleDetailsByTable/{table_id}', 'App\Http\Controllers\Cashier\CashierController@getSaleDetailsByTable');
     
     
     Route::post('/cashier/orderFood', 'App\Http\Controllers\Cashier\CashierController@orderFood');
     
     Route::post('/cashier/deleteSaleDetail', 'App\Http\Controllers\Cashier\CashierController@deleteSaleDetail');
     Route::post('/cashier/increase-quantity', 'App\Http\Controllers\Cashier\CashierController@increaseQuantity');
     Route::post('/cashier/change-quantity', 'App\Http\Controllers\Cashier\CashierController@changesQuantity');
     

     
     Route::post('/cashier/decrease-quantity', 'App\Http\Controllers\Cashier\CashierController@decreaseQuantity');
     
     Route::post('/cashier/confirmOrderStatus','App\Http\Controllers\Cashier\CashierController@confirmOrderStatus' );
     
     Route::post('/cashier/savePayment', 'App\Http\Controllers\Cashier\CashierController@savePayment');
     Route::get('/cashier/showRecipt/{saleID}', 'App\Http\Controllers\Cashier\CashierController@showRecipt');
     

     Route::get('/cashier/printOrderRec/{saleID}', 'App\Http\Controllers\Cashier\CashierController@printOrderRec');

     Route::post('/cashier/printOrder', 'App\Http\Controllers\Cashier\CashierController@printOrder');
     
      // routes for petty cash

    Route::get('pettycash', 'App\Http\Controllers\PettycashController@index')->name('pettycash');
    Route::post('pettycash/store', 'App\Http\Controllers\PettycashController@store')->name(('pettycash.store'));
    Route::get('pettycash/destroy/{id}', 'App\Http\Controllers\PettycashController@destroy')->name(('pettycash.destroy'));

});Route::get('/management',function(){
    return view('management.index'); 
 })->name('management');
 
 
 
 // routes for management
 Route::resource('management/category', App\Http\Controllers\Management\CategoryController::class);
 Route::resource('management/menu', App\Http\Controllers\Management\MenuController::class);
 Route::resource('management/table', App\Http\Controllers\Management\TableController::class);

 // route for inventory
 Route::get('/inventory',function(){
    return view('inventory.index'); 
 })->name('inventory');

 // routes for inventory
Route::resource('inventory/category', App\Http\Controllers\Inventory\CategoryController::class);
Route::resource('inventory/menu', App\Http\Controllers\Inventory\MenuController::class);
Route::resource('inventory/stock', App\Http\Controllers\Inventory\StockController::class);

Route::post('inventory/stockFilterByCategory', 'App\Http\Controllers\Inventory\StockController@index')->name('Stock.stockFilterByCategory');

Route::get('inventory/stock/{itemid}', 'App\Http\Controllers\Inventory\StockController@show')->name('Stock.show');
Route::get('/stock/monthly', [InventoryController::class, 'viewMonthlyStock'])->name('stock.monthly');

Route::resource('inventory/table', App\Http\Controllers\Inventory\TableController::class);

Route::get('/categories-products', [InventoryController::class, 'categoriesProducts'])
    ->name('categories-products.index');



// routes for calender
Route::get('/calendar', function () {
   return view('calendar');
})->name('calendar');
Route::get('/bookings', [BookingController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::put('/bookings/{id}', [BookingController::class, 'update']);
Route::get('/available-rooms', [BookingController::class, 'availableRooms']);
Route::get('/booking-logs', [BookingController::class, 'getLogs']);
Route::get('/bookings/{id}/print', [BookingController::class, 'printConfirmation'])->name('bookings.print');

// routes for inventory
Route::get('/stock', [InventoryController::class, 'index'])->name('stock.index');
Route::post('/stock', [InventoryController::class, 'store'])->name('stock.store');
Route::post('/categories', [InventoryController::class, 'storeCategory'])->name('categories.store');
Route::post('/items', [InventoryController::class, 'storeItem'])->name('items.store');
Route::post('/stock/monthly', [InventoryController::class, 'viewMonthlyStock'])->name('stock.monthly');
Route::post('/stock/update', [InventoryController::class, 'updateTodayStock'])->name('stock.update');
Route::get('/stock/test-propagation', [InventoryController::class, 'checkStockPropagation'])->name('stock.test-propagation');

// routes for inventory for physical Items
// Inventory Dashboard
Route::get('/inv-inventory', [InvInventoryController::class, 'index'])->name('inv_inventory.index');
// Store Category
Route::post('/inv-inventory/categories', [InvInventoryController::class, 'storeCategory'])->name('inv_inventory.categories.store');
// Store Product
Route::post('/inv-inventory/products', [InvInventoryController::class, 'storeProduct'])->name('inv_inventory.products.store');
// Update Today's Stock
Route::post('/inv-inventory/update', [InvInventoryController::class, 'updateTodayStock'])->name('inv_inventory.update');
// View Monthly Stock
Route::get('/inv-inventory/monthly', [InvInventoryController::class, 'viewMonthlyStock'])->name('inv_inventory.monthly');




// Vehicle Security Routes
Route::middleware(['auth'])->group(function () {
   Route::resource('vehicle-security', VehicleSecurityController::class);
   Route::get('vehicle-security/date/{date?}', [VehicleSecurityController::class, 'showByDate'])
    ->name('vehicle-security.by-date');
    Route::post('vehicle-security/{id}/checkout', [VehicleSecurityController::class, 'checkout'])->name('vehicle-security.checkout');
    Route::post('vehicle-security/{id}/temp-checkout', [VehicleSecurityController::class, 'tempCheckout'])->name('vehicle-security.temp-checkout');
    Route::post('vehicle-security/{id}/temp-checkin', [VehicleSecurityController::class, 'tempCheckin'])->name('vehicle-security.temp-checkin');
    Route::post('vehicle-security/{id}/update-team', [VehicleSecurityController::class, 'updateTeam'])->name('vehicle-security.update-team');
    Route::get('vehicle-security/available-rooms', [VehicleSecurityController::class, 'getAvailableRooms'])
    ->name('vehicle-security.available-rooms');
    Route::put('vehicle-security/{id}/update', [VehicleSecurityController::class, 'update'])->name('vehicle-security.update');
});




// Expenses
Route::middleware(['auth'])->group(function () {
   // Resource routes for groups and persons
   Route::resource('groups', GroupController::class);
   Route::resource('persons', PersonController::class);
   
   // Costs routes with print functionality
   Route::resource('costs', CostController::class)->except(['show']);
   Route::get('costs/print-daily', [CostController::class, 'printDailyExpenses'])->name('costs.print.daily');
   Route::get('costs/{cost}/print', [CostController::class, 'printTransaction'])->name('costs.print.transaction');
});
// Task
// Home Route

// Task Routes
Route::resource('tasks', TaskController::class);
Route::post('tasks/{id}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
// Task Category Routes
Route::resource('task-categories', TaskCategoryController::class);
Route::get('completed-tasks', [CompletedTaskController::class, 'index'])->name('completed-tasks.index');


Route::middleware(['auth'])->group(function () {
   Route::get('/staff/attendance', [StaffAttendanceController::class, 'index'])->name('staff.attendance.index');
   Route::post('/staff/attendance', [StaffAttendanceController::class, 'store'])->name('staff.attendance.store');
   Route::post('/staff/attendance/checkout', [StaffAttendanceController::class, 'checkOut'])->name('staff.attendance.checkout');
   Route::get('/staff/attendance/report', [StaffAttendanceController::class, 'report'])->name('staff.attendance.report');
});


// routes/api.php
Route::post('/fingerprint/attendance', [FingerprintDeviceController::class, 'processAttendance']);


Route::middleware(['web', 'auth'])->group(function () {
   Route::post('/staff/attendance/import', [StaffAttendanceController::class, 'import'])->name('staff.attendance.import');
});

//new attendance


// Then update the routes to include the full namespace:
Route::middleware(['auth'])->group(function () {
    Route::get('/manual-attendance', [ManualAttendanceController::class, 'index'])->name('attendance.manual.index');
    Route::post('/manual-attendance/mark', [ManualAttendanceController::class, 'markAttendance'])->name('attendance.manual.mark');
    Route::post('/manual-attendance/checkout', [ManualAttendanceController::class, 'markCheckout'])->name('attendance.manual.checkout');
    Route::get('/manual-attendance/report', [ManualAttendanceController::class, 'report'])->name('attendance.manual.report');
});


// Package Management Routes
Route::middleware(['auth'])->group(function () {
   Route::resource('package-categories', PackageCategoryController::class);
   Route::resource('packages', PackageController::class);
   Route::get('/packages/{package}/print', [PackageController::class, 'print'])->name('packages.print');
});

// Quotation Management Routes
Route::middleware(['auth'])->group(function () {
   // Existing quotation resource routes
   Route::resource('quotations', QuotationController::class);
   
   // Add these new routes for quotation conversion and printing
   Route::post('/quotations/{quotation}/convert', [QuotationController::class, 'convertToBooking'])
       ->name('quotations.convert-to-booking');
   Route::get('/quotations/{quotation}/print', [QuotationController::class, 'print'])
       ->name('quotations.print');



});








// Damage Items Routes
Route::get('/damage-items', [DamageItemController::class, 'index'])->name('damage-items.index');
Route::post('/damage-items', [DamageItemController::class, 'store'])->name('damage-items.store');
Route::get('/damage-items/monthly-report', [DamageItemController::class, 'monthlyReport'])->name('damage-items.monthly-report');

// Room Availability Management Routes
Route::get('/rooms/availability', [RoomAvailabilityController::class, 'index'])->name('rooms.availability');
Route::post('/rooms/store', [RoomAvailabilityController::class, 'storeRoom'])->name('rooms.store');
Route::post('/rooms/checklist-items', [RoomAvailabilityController::class, 'storeChecklistItem'])->name('rooms.checklist.store');
Route::post('/rooms/{room}/daily-check', [RoomAvailabilityController::class, 'dailyCheck'])->name('rooms.daily-check');
Route::post('/rooms/{room}/toggle-booking', [RoomAvailabilityController::class, 'toggleBooking'])->name('rooms.toggle-booking');
Route::post('/rooms/{room}/update-checklist', [RoomAvailabilityController::class, 'updateChecklist'])
    ->name('rooms.update-checklist');

    Route::post('/rooms/{room}/guest-in', [RoomAvailabilityController::class, 'guestIn'])->name('rooms.guest-in');
    Route::post('/rooms/{room}/guest-out', [RoomAvailabilityController::class, 'guestOut'])->name('rooms.guest-out');
    

Route::middleware(['auth', 'VerifyAdmin'])->group(function(){

 
     
     // routes for management
   
     Route::resource('management/user',App\Http\Controllers\Management\UserController::class);

     //route for report
     
     Route::get('/report', 'App\Http\Controllers\Report\ReportController@index')->name('report');
     
     
     Route::get('/report/show', 'App\Http\Controllers\Report\ReportController@show');
     
     // cashier
   
     // routes for inventory
     Route::post('inventory/storestock/{itemid}', 'App\Http\Controllers\Inventory\StockController@store')->name('Stock.storeStock');
     Route::delete('inventory/removeStock/{itemid}', 'App\Http\Controllers\Inventory\StockController@destroy')->name('Stock.removeStock');


     // Export to excel
     Route::get('/report/show/export', 'App\Http\Controllers\Report\ReportController@export');
     
    
//salary
Route::middleware(['auth'])->group(function () {
   Route::get('/salary', [SalaryController::class, 'index'])->name('salary.index');
   Route::post('/salary/calculate', [SalaryController::class, 'calculate'])->name('salary.calculate');
   Route::get('/salary/{salary}/payslip', [SalaryController::class, 'payslip'])->name('salary.payslip');
   Route::get('/salary/generate-payslip', [SalaryController::class, 'generatePayslip'])
    ->name('salary.generatePayslip');
    Route::get('/salary/{id}/payslip', [SalaryController::class, 'generatePayslip'])->name('salary.payslip');
   Route::get('/salary/basic', [SalaryController::class, 'basicSalary'])->name('salary.basic');
   Route::post('/salary/update-basic', [SalaryController::class, 'updateBasic'])->name('salary.updateBasic');
   Route::post('/person/update-basic-salary', [SalaryController::class, 'updateBasicSalary'])->name('person.updateBasicSalary');
});


//S/C
Route::middleware(['auth'])->group(function () {
    // Main index page for service charge
    Route::get('/service-charge', [ServiceChargeController::class, 'index'])
        ->name('service-charge.index');
 
    // Update points for an employee
    Route::post('/service-charge/update-points', [ServiceChargeController::class, 'updatePoints'])
        ->name('service-charge.updatePoints');
 
    // Generate service charge for a specific employee
    Route::post('/service-charge/generate', [ServiceChargeController::class, 'generateServiceCharge'])
        ->name('service-charge.generate');

     
 
    // Print service charge receipt for a specific service charge record
    Route::get('/service-charge/{id}/print', [ServiceChargeController::class, 'printServiceCharge'])
        ->name('service-charge.print');
 
        Route::get('/service-charge/points', [ServiceChargeController::class, 'managePoints'])
     ->name('service-charge.points');
 Route::post('/service-charge/points/update-bulk', [ServiceChargeController::class, 'updatePointsBulk'])
     ->name('service-charge.points.update-bulk');

               // Cashier Balance Routes
Route::prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/balance', [CashierBalanceController::class, 'index'])->name('balance');
    Route::post('/balance/update-opening', [CashierBalanceController::class, 'updateOpeningBalance'])->name('update-opening-balance');
    Route::post('/balance/add-transaction', [CashierBalanceController::class, 'addManualTransaction'])->name('add-manual-transaction');
    Route::post('/balance/close-day', [CashierBalanceController::class, 'closeDay'])->name('close-day');
    Route::get('/balance/report', [CashierBalanceController::class, 'generateReport'])->name('report');
});
 });

    });
