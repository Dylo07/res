@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <!-- Card for Navigation -->
    <div class="card mb-4">
        <div class="card-body p-0">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs border-bottom-0">
                <li class="nav-item">
                    <a href="{{ route('stock.index') }}" 
                       class="nav-link {{ Request::routeIs('stock.index') ? 'active' : '' }}">
                        Stock Management
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('categories-products.index') }}" 
                       class="nav-link {{ Request::routeIs('categories-products.index') ? 'active' : '' }}">
                        Category & Product Management
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Category Selection Dropdown -->
    <form action="{{ route('stock.index') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="category_filter" class="form-label">Select Category</label>
                <select name="category_id" id="category_filter" class="form-select" onchange="this.form.submit()">
                    <option value="">Please select a category</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('category_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="month" value="{{ $currentMonth }}">
            <input type="hidden" name="year" value="{{ $currentYear }}">
        </div>
    </form>

    <!-- Month Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <form action="{{ route('stock.index') }}" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                
                @php
                    $currentDate = Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1);
                    $previousMonth = $currentDate->copy()->subMonth();
                    $nextMonth = $currentDate->copy()->addMonth();
                @endphp
                
                <div class="col-auto">
                    <div class="btn-group">
                        <a href="{{ route('stock.index', [
                            'month' => $previousMonth->month,
                            'year' => $previousMonth->year,
                            'category_id' => request('category_id')
                        ]) }}" 
                        class="btn btn-outline-primary">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                        
                        <button type="button" class="btn btn-outline-primary" disabled>
                            {{ $currentDate->format('F Y') }}
                        </button>

                        @if($nextMonth->lte(now()))
                        <a href="{{ route('stock.index', [
                            'month' => $nextMonth->month,
                            'year' => $nextMonth->year,
                            'category_id' => request('category_id')
                        ]) }}" 
                        class="btn btn-outline-primary">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="col-auto">
                    <select name="month" class="form-select" onchange="this.form.submit()">
                        @foreach(range(1, 12) as $m)
                            @php
                                $monthDate = Carbon\Carbon::createFromDate($currentYear, $m, 1);
                                $isDisabled = $monthDate->isAfter(now());
                            @endphp
                            <option value="{{ $m }}" 
                                    {{ $currentMonth == $m ? 'selected' : '' }}
                                    {{ $isDisabled ? 'disabled' : '' }}>
                                {{ $monthDate->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <select name="year" class="form-select" onchange="this.form.submit()">
                        @foreach(range(now()->subYears(2)->year, now()->year) as $y)
                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <button type="submit" name="month" value="{{ now()->month }}" class="btn btn-secondary">
                        Current Month
                    </button>
                    <button type="submit" name="month" value="{{ now()->subMonth()->month }}" 
                            class="btn btn-outline-secondary"
                            onclick="this.form.year.value='{{ now()->subMonth()->year }}'">
                        Last Month
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedGroup)
        <!-- Stock Table for Selected Category -->
        <h4>{{ $selectedGroup->name }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item</th>
                        @for($i = 1; $i <= 31; $i++)
                            <th>{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($selectedGroup->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            @for($i = 1; $i <= 31; $i++)
                                @php
                                    $date = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $i);
                                    $inventory = $item->inventory->firstWhere('stock_date', $date);
                                    
                                    if ($inventory) {
                                        $displayStock = $inventory->stock_level;
                                    } else {
                                        $previousInventory = $item->inventory
                                            ->where('stock_date', '<', $date)
                                            ->sortByDesc('stock_date')
                                            ->first();
                                        
                                        if ($i === 1 && !$inventory && $previousInventory) {
                                            $displayStock = $previousInventory->stock_level;
                                        } else {
                                            $displayStock = $previousInventory ? $previousInventory->stock_level : '-';
                                        }
                                    }
                                    
                                    $stockLogs = $monthLogs->where('item_id', $item->id)
    ->filter(function($log) use ($date) {
        return $log->created_at->format('Y-m-d') === $date;
    });
                                    $additions = $stockLogs->where('action', 'add')->sum('quantity');
                                    $removals = $stockLogs->where('action', 'remove')->sum('quantity');
                                @endphp
                                <td>
                                    @if($date <= now()->toDateString())
                                        <div>{{ $displayStock }}</div>
                                        @if($additions)
                                            <div class="text-success">+{{ $additions }}</div>
                                        @endif
                                        @if($removals)
                                            <div class="text-danger">-{{ $removals }}</div>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Stock Update Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Update Stock</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('stock.update') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label for="item" class="form-label">Item</label>
                        <select name="item_id" id="item" class="form-select" required>
                            @foreach($selectedGroup->items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" 
                               step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" name="description" id="description" class="form-control" 
                               placeholder="Enter a description" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="add" class="btn btn-success">Add</button>
                        <button type="submit" name="action" value="remove" class="btn btn-danger">Remove</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Log Details Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Stock Log Details</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('stock.index') }}" method="GET" class="mb-3">
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="log_date" class="form-label">Select Date</label>
                            <input type="date" id="log_date" name="log_date" class="form-control" 
                                   value="{{ request('log_date', now()->toDateString()) }}"
                                   max="{{ now()->toDateString() }}"
                                   onchange="this.form.submit()">
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Item</th>
                                <th>Action</th>
                                <th>Quantity</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('H:i') }}</td>
                                    <td>{{ $log->user->name }}</td>
                                    <td>{{ $log->item->name }}</td>
                                    <td>{{ ucfirst($log->action) }}</td>
                                    <td>{{ $log->quantity }}</td>
                                    <td>{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No stock movements on this date</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $logs->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            Please select a category to view stock details
        </div>
    @endif
</div>

<style>
td {
    padding: 8px !important;
    font-size: 0.9em;
}
td div {
    line-height: 1.2;
}
.text-success {
    color: #28a745 !important;
    font-weight: bold;
}
.text-danger {
    color: #dc3545 !important;
    font-weight: bold;
}
.nav-tabs {
    border-bottom: none;
    padding: 0.5rem 1rem 0;
}

.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    padding: 0.75rem 1rem;
    margin-right: 0.5rem;
}

.nav-tabs .nav-link:hover {
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
    background: transparent;
}

.card {
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
@endsection