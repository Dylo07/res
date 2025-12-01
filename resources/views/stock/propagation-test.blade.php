@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Stock Propagation Test</h2>

    <!-- Test Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('stock.test-propagation') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="item_id" class="form-label">Select Item</label>
                    <select name="item_id" id="item_id" class="form-select" required>
                        @foreach($groups as $group)
                            <optgroup label="{{ $group->name }}">
                                @foreach($group->items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ now()->toDateString() }}" required>
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ now()->addDays(7)->toDateString() }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Check Stock</button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($stockLevels))
    <!-- Results Table -->
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Stock Levels for {{ $item->name }}</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Stock Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockLevels as $stock)
                            <tr>
                                <td>{{ $stock['date'] }}</td>
                                <td>{{ $stock['stock_level'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No stock data available for the selected date range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Add this button to your existing stock management page -->
<div class="mt-3">
    <a href="{{ route('stock.test-propagation') }}" class="btn btn-info">
        Test Stock Propagation
    </a>
</div>
@endsection