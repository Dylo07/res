<?php

namespace App\Http\Controllers;

use App\Models\CashierBalance;
use App\Models\CashierManualTransaction;
use App\Models\Sale;
use App\Models\Cost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CashierBalanceController extends Controller
{
    public function index(Request $request)
    {
        // Get selected date or default to today
        $selectedDate = $request->date ? Carbon::parse($request->date) : Carbon::now();
        $formattedDate = $selectedDate->format('Y-m-d');

        // Get balance for selected date
        $currentBalance = CashierBalance::firstOrCreate(
            ['date' => $formattedDate],
            [
                'opening_balance' => $this->getPreviousClosingBalance($selectedDate),
                'closing_balance' => 0,
                'created_by' => Auth::id()
            ]
        );

        // Calculate balances for the selected date
        $todaySales = $this->calculateDaySales($selectedDate);
        $todayExpenses = $this->calculateDayExpenses($selectedDate);
        $currentBalance = $this->updateBalanceCalculations($currentBalance, $todaySales, $todayExpenses);

        // Get previous day's balance
        $previousDate = $selectedDate->copy()->subDay();
        $previousBalance = CashierBalance::where('date', $previousDate->format('Y-m-d'))->first();

        // Get manual transactions for the selected date
        $manualTransactions = CashierManualTransaction::where('cashier_balance_id', $currentBalance->id)
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cashier.balance', compact(
            'currentBalance',
            'previousBalance',
            'manualTransactions',
            'selectedDate'
        ));
    }

    private function calculateDaySales($date)
    {
        // Format dates exactly like in ReportController
        $startDateTime = date("Y-m-d H:i:s", strtotime($date->format('Y-m-d').' 00:00:00'));
        $endDateTime = date("Y-m-d H:i:s", strtotime($date->format('Y-m-d').' 23:59:59'));

        // Calculate total sales using the same method as ReportController
        $sales = Sale::whereBetween('updated_at', [$startDateTime, $endDateTime])
            ->where('sale_status', 'paid');

        // Return total using the same calculation method as report
        return $sales->sum('change');
    }

    private function calculateDayExpenses($date)
    {
        // Format dates to match the same time range
        $startDateTime = date("Y-m-d H:i:s", strtotime($date->format('Y-m-d').' 00:00:00'));
        $endDateTime = date("Y-m-d H:i:s", strtotime($date->format('Y-m-d').' 23:59:59'));

        return Cost::whereBetween('cost_date', [$startDateTime, $endDateTime])
            ->sum('amount');
    }

    private function getPreviousClosingBalance($date)
    {
        $previousDay = $date->copy()->subDay()->format('Y-m-d');
        $previousBalance = CashierBalance::where('date', $previousDay)->first();
        return $previousBalance ? $previousBalance->closing_balance : 0;
    }

    private function updateBalanceCalculations($balance, $sales, $expenses)
    {
        $currentBalance = $balance->opening_balance + 
                         $sales + 
                         $balance->additional_earnings - 
                         $expenses - 
                         $balance->manual_expenses;

        $balance->update([
            'total_sales' => $sales,
            'total_expenses' => $expenses,
            'closing_balance' => $currentBalance,
            'updated_by' => Auth::id()
        ]);

        return $balance->fresh();
    }

    public function updateOpeningBalance(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $balance = CashierBalance::where('date', $date)->firstOrFail();
            
            $balance->update([
                'opening_balance' => $request->opening_balance,
                'notes' => $request->notes,
                'updated_by' => Auth::id()
            ]);

            // Recalculate the day's balance
            $sales = $this->calculateDaySales(Carbon::parse($date));
            $expenses = $this->calculateDayExpenses(Carbon::parse($date));
            $this->updateBalanceCalculations($balance, $sales, $expenses);

            DB::commit();
            return redirect()->back()->with('success', 'Opening balance updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update opening balance: ' . $e->getMessage());
        }
    }

    public function addManualTransaction(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:earning,expense',
            'notes' => 'required|string|max:500',
            'date' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $balance = CashierBalance::where('date', $date)->firstOrFail();

            $transaction = new CashierManualTransaction([
                'type' => $request->type,
                'amount' => $request->amount,
                'notes' => $request->notes,
                'created_by' => Auth::id()
            ]);
            
            $balance->manualTransactions()->save($transaction);

            if ($request->type === 'earning') {
                $balance->additional_earnings += $request->amount;
            } else {
                $balance->manual_expenses += $request->amount;
            }
            
            $balance->updated_by = Auth::id();
            $balance->save();

            // Recalculate the day's balance
            $sales = $this->calculateDaySales(Carbon::parse($date));
            $expenses = $this->calculateDayExpenses(Carbon::parse($date));
            $this->updateBalanceCalculations($balance, $sales, $expenses);

            DB::commit();
            return redirect()->back()->with('success', 'Transaction added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to add transaction: ' . $e->getMessage());
        }
    }

    public function closeDay(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $balance = CashierBalance::where('date', $date)->firstOrFail();
            
            // Do final calculations
            $sales = $this->calculateDaySales(Carbon::parse($date));
            $expenses = $this->calculateDayExpenses(Carbon::parse($date));
            $this->updateBalanceCalculations($balance, $sales, $expenses);
            
            // Close the day
            $balance->update([
                'status' => 'closed',
                'notes' => $request->notes,
                'updated_by' => Auth::id()
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Day closed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to close day: ' . $e->getMessage());
        }
    }
}