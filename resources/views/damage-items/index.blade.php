<!-- resources/views/damage-items/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary">Damaged/Missing Items Management</h5>
            <input type="month" 
                   name="month" 
                   class="form-control w-auto" 
                   value="{{ request('month', date('Y-m')) }}" 
                   onchange="this.form.submit()"
                   style="border-radius: 20px;">
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Add New Item Form -->
            <form action="{{ route('damage-items.store') }}" method="POST" class="mb-4">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Item Name</label>
                        <input type="text" name="item_name" class="form-control" required placeholder="Enter item name">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" required placeholder="Qty">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit Price</label>
                        <div class="input-group">
                            <span class="input-group-text">Rs.</span>
                            <input type="number" name="unit_price" class="form-control" step="0.01" min="0" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control" required>
                            <option value="damaged">Damaged</option>
                            <option value="missing">Missing</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date</label>
                        <input type="date" name="reported_date" class="form-control" required>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Add</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Add any additional notes here..."></textarea>
                    </div>
                </div>
            </form>

            <!-- Items Table -->
            <div class="table-responsive mt-4">
                <h6 class="text-muted mb-3">Items for {{ Carbon\Carbon::parse(request('month', date('Y-m')))->format('F Y') }}</h6>
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Item Name</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Cost</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td>{{ $item->reported_date->format('Y-m-d') }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>
                                <span class="badge {{ $item->type === 'damaged' ? 'bg-warning' : 'bg-danger' }} text-dark">
                                    {{ ucfirst($item->type) }}
                                </span>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rs. {{ number_format($item->unit_price, 2) }}</td>
                            <td>Rs. {{ number_format($item->total_cost, 2) }}</td>
                            <td>{{ $item->notes }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No items recorded for this month</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end"><strong>Total Cost:</strong></td>
                            <td colspan="2"><strong>Rs. {{ number_format($totalCost, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}
.form-control {
    border-radius: 5px;
}
.form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
}
.btn-primary {
    background-color: #4f46e5;
    border-color: #4f46e5;
}
.btn-primary:hover {
    background-color: #4338ca;
    border-color: #4338ca;
}
.table {
    vertical-align: middle;
}
.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
}
</style>
@endsection