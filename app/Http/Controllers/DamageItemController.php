<?php

namespace App\Http\Controllers;

use App\Models\DamageItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DamageItemController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        
        $items = DamageItem::whereYear('reported_date', Carbon::parse($month)->year)
            ->whereMonth('reported_date', Carbon::parse($month)->month)
            ->orderBy('reported_date', 'desc')
            ->get();
            
        $totalCost = $items->sum('total_cost');
        
        return view('damage-items.index', compact('items', 'totalCost'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'type' => 'required|in:damaged,missing',
            'notes' => 'nullable|string',
            'reported_date' => 'required|date'
        ]);

        DamageItem::create($validated);

        return redirect()->route('damage-items.index')
            ->with('success', 'Item recorded successfully');
    }
}