@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" type="button" role="tab">
                Stock Management
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="management-tab" data-bs-toggle="tab" data-bs-target="#management" type="button" role="tab">
                Category & Product Management
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Stock Management Tab -->
        <div class="tab-pane fade show active" id="stock" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Physical Item Inventory</h2>
                <a href="{{ route('inv_inventory.monthly', ['month' => $currentMonth, 'year' => $currentYear]) }}" 
                   class="btn btn-primary">
                    View Monthly Stock Details
                </a>
            </div>
<!-- Stock Navigation -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Stock Navigation</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('inv_inventory.index') }}" method="GET">
            <div class="row g-3">
                <!-- Category Filter -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="category_filter" class="form-label fw-bold">Select Category</label>
                        <select name="category_id" id="category_filter" class="form-select border-primary" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                     <!-- Month Selection -->
                <div class="col-md-3">
                    <label for="month" class="form-label">Month</label>
                    <select name="month" id="month" class="form-select" onchange="this.form.submit()">
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

                     <!-- Year Selection -->
                <div class="col-md-3">
                    <label for="year" class="form-label">Year</label>
                    <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                        @foreach(range(now()->subYears(2)->year, now()->year) as $y)
                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>


                    <!-- Quick Navigation -->
                <div class="col-md-3 align-self-end">
                    <button type="submit" name="month" value="{{ now()->month }}" 
                            class="btn btn-secondary me-2">Current Month</button>
                    <button type="submit" name="month" value="{{ now()->subMonth()->month }}" 
                            class="btn btn-outline-secondary"
                            onclick="this.form.year.value='{{ now()->subMonth()->year }}'">
                        Last Month
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
            <!-- Stock Update Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Update Today's Stock</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('inv_inventory.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="product" class="form-label">Product</label>
                                <select name="product_id" id="product" class="form-select" required>
                                    @foreach($categories as $category)
                                        @if($category->products->isNotEmpty())
                                            <optgroup label="{{ $category->name }}">
                                                @foreach($category->products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" 
                                       step="0.01" min="0.01" required>
                            </div>
                            <div class="col-md-3">
                                <label for="description" class="form-label">Description</label>
                                <input type="text" name="description" id="description" class="form-control" 
                                       placeholder="Enter a description" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" name="action" value="add" class="btn btn-success me-2">Add Stock</button>
                            <button type="submit" name="action" value="remove" class="btn btn-danger">Remove Stock</button>
                        </div>
                    </form>
                </div>
            </div>

          <!-- Stock Overview Section -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">
            Stock Overview - {{ Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F Y') }}
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            @php
                $daysInMonth = Carbon\Carbon::create($currentYear, $currentMonth)->daysInMonth;
            @endphp

            @if(request('category_id'))
                @php
                    $selectedCategory = $categories->firstWhere('id', request('category_id'));
                @endphp
                @if($selectedCategory)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="position-sticky start-0 bg-light">Product</th>
                                @for($i = 1; $i <= $daysInMonth; $i++)
                                    <th>{{ $i }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedCategory->products as $product)
                                <tr>
                                    <td class="position-sticky start-0 bg-light">{{ $product->name }}</td>
                                    @for($i = 1; $i <= $daysInMonth; $i++)
                                        @php
                                            $date = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $i);
                                            $inventory = $product->inventories->firstWhere('stock_date', $date);
                                            
                                            if ($inventory) {
                                                $displayStock = $inventory->stock_level;
                                            } else {
                                                $previousInventory = $product->inventories
                                                    ->where('stock_date', '<', $date)
                                                    ->sortByDesc('stock_date')
                                                    ->first();
                                                
                                                if ($i === 1 && !$inventory && $previousInventory) {
                                                    $displayStock = $previousInventory->stock_level;
                                                } else {
                                                    $displayStock = $previousInventory ? $previousInventory->stock_level : '-';
                                                }
                                            }

                                            $stockLogs = $monthLogs->where('product_id', $product->id)
                                                ->filter(function($log) use ($date) {
                                                    return $log->created_at->format('Y-m-d') === $date;
                                                });
                                            
                                            $additions = $stockLogs->where('action', 'add')->sum('quantity');
                                            $removals = $stockLogs->where('action', 'remove')->sum('quantity');
                                        @endphp
                                        <td class="{{ $date == now()->toDateString() ? 'table-primary' : '' }}">
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
                @endif
            @else
                <div class="alert alert-info">
                    Please select a category to view stock details
                </div>
            @endif
        </div>
    </div>
</div>
            <!-- Log Details Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Stock Movement Log</h3>
                    <div class="col-auto">
                        <input type="date" id="log_date" name="log_date" class="form-control" 
                               value="{{ request('log_date', now()->toDateString()) }}"
                               max="{{ now()->toDateString() }}"
                               onchange="window.location.href='{{ route('inv_inventory.index') }}?log_date=' + this.value 
                               + '&category_id={{ request('category_id') }}'">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Product</th>
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
                                        <td>{{ $log->product->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $log->action === 'add' ? 'success' : 'danger' }}">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
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
                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $logs->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Category & Product Management Tab -->
        <div class="tab-pane fade" id="management" role="tabpanel">
            <div class="row">
                <!-- Category Management -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Category Management</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('inv_inventory.categories.store') }}" method="POST" class="mb-4">
                                @csrf
                                <div class="mb-3">
                                    <label for="category_name" class="form-label">New Category Name</label>
                                    <input type="text" name="name" id="category_name" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-success">Add Category</button>
                            </form>

                            <h5 class="mt-4">Existing Categories</h5>
                            <div class="list-group">
                                @foreach($categories as $category)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $category->name }}
                                        <span class="badge bg-primary rounded-pill">
                                            {{ $category->products->count() }} products
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Management -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Product Management</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('inv_inventory.products.store') }}" method="POST" class="mb-4">
                                @csrf
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">New Product Name</label>
                                    <input type="text" name="name" id="product_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="product_category" class="form-label">Category</label>
                                    <select name="category_id" id="product_category" class="form-select" required>
                                        <option value="">Select category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Add Product</button>
                            </form>

                            <h5 class="mt-4">Existing Products</h5>
                            <div class="accordion" id="productsAccordion">
                                @foreach($categories as $category)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse{{ $category->id }}">
                                                {{ $category->name }} ({{ $category->products->count() }})
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $category->id }}" class="accordion-collapse collapse" 
                                             data-bs-parent="#productsAccordion">
                                            <div class="accordion-body">
                                            <ul class="list-group">
                                                    @forelse($category->products as $product)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $product->name }}
                                                            <span class="badge bg-secondary rounded-pill">
                                                                Current Stock: 
                                                                {{ $product->inventories->where('stock_date', now()->toDateString())->first()?->stock_level ?? '0' }}
                                                            </span>
                                                        </li>
                                                    @empty
                                                        <li class="list-group-item text-muted">
                                                            No products in this category
                                                        </li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-responsive {
    overflow-x: auto;
    max-height: 600px;
}
.position-sticky {
    position: sticky;
    z-index: 1;
}
.start-0 {
    left: 0;
}
.badge {
    font-size: 0.85em;
}
.accordion-button:not(.collapsed) {
    background-color: #e7f1ff;
}
.table td, .table th {
    white-space: nowrap;
    padding: 0.5rem !important;
}
.nav-tabs .nav-link {
    color: #495057;
}
.nav-tabs .nav-link.active {
    font-weight: bold;
    color: #000;
}

/* Add to your existing styles */
.text-success {
    color: #28a745 !important;
    font-weight: bold;
    font-size: 0.85em;
}
.text-danger {
    color: #dc3545 !important;
    font-weight: bold;
    font-size: 0.85em;
}
td div {
    line-height: 1.2;
}
.table-responsive {
    overflow-x: auto;
    max-height: 600px;
}
.position-sticky {
    position: sticky;
    z-index: 1;
}
.start-0 {
    left: 0;
}
.form-select.border-primary {
    border-width: 2px;
}

.form-label.fw-bold {
    color: #0d6efd;
    font-size: 1rem;
}

.bg-light {
    background-color: #f8f9fa !important;
}
</style>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Keep selected tab active after page reload
        let activeTab = sessionStorage.getItem('activeInventoryTab');
        if (activeTab) {
            const tab = new bootstrap.Tab(document.querySelector(`[data-bs-target="${activeTab}"]`));
            tab.show();
        }

        // Store the active tab
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(button => {
            button.addEventListener('shown.bs.tab', function (event) {
                sessionStorage.setItem('activeInventoryTab', event.target.getAttribute('data-bs-target'));
            });
        });

        // Highlight today's column in stock table
        const today = new Date().getDate();
        document.querySelectorAll('table thead tr th').forEach((th, index) => {
            if (parseInt(th.textContent) === today) {
                document.querySelectorAll(`table tbody tr td:nth-child(${index + 1})`).forEach(td => {
                    td.classList.add('table-primary');
                });
            }
        });
    });
</script>
@endpush
@endsection