@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-4xl font-bold mb-4">Hotel Expenses</h1>

<div class="mb-4">
    <a href="{{ route('costs.create') }}" class="btn btn-primary bg-blue-500 text-white px-4 py-2 rounded-md">Add New Expense</a>
    <a href="{{ route('groups.create') }}" class="btn btn-info bg-blue-400 text-white px-4 py-2 rounded-md">Add New Category</a>
    <a href="{{ route('persons.create') }}" class="btn btn-secondary bg-gray-500 text-white px-4 py-2 rounded-md">Add New Person/Shop</a>
</div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Analytics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Expenses</h5>
                    <h3 class="card-text">Rs. {{ number_format($analytics['total_amount'], 2) }}</h3>
                    <small>{{ $analytics['trend_percentage'] }}% from last month</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Transactions</h5>
                    <h3 class="card-text">{{ $analytics['total_transactions'] }}</h3>
                    <small>Avg. Rs. {{ number_format($analytics['avg_transaction'], 2) }}/transaction</small>
                </div>
            </div>
        </div>
        <!-- In your index.blade.php, update the Top Category card -->
<div class="col-md-3">
    <div class="card bg-info text-white">
        <div class="card-body">
            <h5 class="card-title">Top Category</h5>
            <h3 class="card-text">{{ $analytics['top_category']['name'] }}</h3>
            @if($analytics['top_category']['total'] > 0)
                <small>Rs. {{ number_format($analytics['top_category']['total'], 2) }} 
                      ({{ $analytics['top_category']['count'] }} transactions)</small>
            @else
                <small>No expenses recorded</small>
            @endif
        </div>
    </div>
</div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Daily Total</h5>
                    <h3 class="card-text">Rs. {{ number_format($analytics['daily_total'], 2) }}</h3>
                    <small>{{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}</small>
                </div>
            </div>
        </div>
    </div>

   <!-- Charts Section -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Expense Distribution by Category</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Daily Expenses Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>



    <!-- Category Totals Section -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0">Category Totals</h3>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($analytics['category_breakdown'] as $category)
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $category['name'] }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $category['count'] }} transactions</h6>
                        <p class="card-text font-weight-bold">Rs. {{ number_format($category['total'], 2) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

    <!-- Month Selector -->
    <form action="{{ route('costs.index') }}" method="GET" class="mb-3">
        <div class="form-group row">
            <label for="month" class="col-form-label col-sm-2">Select Month</label>
            <div class="col-sm-4">
                <input type="month" name="month" id="month" class="form-control" value="{{ $month }}">
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </div>
    </form>

    <!-- Summary of Expenses of the Month -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Summary of Expenses of {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Category</th>
                        <th>Person/Shop</th>
                        <th>Expense</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                @php $groupIndex = 0; @endphp
                @foreach ($monthlyGroupedCosts as $group => $persons)
                    <!-- Category Row -->
                    <tr data-toggle="collapse" data-target=".group-{{ $groupIndex }}" class="clickable collapsed table-secondary">
                        <td colspan="4">
                            <strong>{{ $group }}</strong>
                            <span class="float-right">&#x25BC;</span>
                        </td>
                    </tr>
                    @foreach ($persons as $person => $data)
                        <!-- Person/Shop Row -->
                        <tr class="collapse group-{{ $groupIndex }} table-light">
                            <td></td>
                            <td colspan="4"><strong>{{ $person }}</strong></td>
                        </tr>
                        @foreach ($data['costs'] as $cost)
                            <!-- Expense Row -->
                            <tr class="collapse group-{{ $groupIndex }}">
                                <td></td>
                                <td></td>
                                <td>Rs. {{ number_format($cost->amount, 2) }}</td>
                                <td>{{ $cost->cost_date->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                        <!-- Total for Person/Shop -->
                        <tr class="collapse group-{{ $groupIndex }} table-info">
                            <td></td>
                            <td colspan="2" class="text-end"><strong>Total for {{ $person }}</strong></td>
                            <td><strong>Rs. {{ number_format($data['total'], 2) }}</strong></td>
                        </tr>
                    @endforeach
                    @php $groupIndex++; @endphp
                @endforeach
                <!-- Grand Total -->
                <tr class="table-primary">
                    <td colspan="3" class="text-end"><strong>Grand Total</strong></td>
                    <td><strong>Rs. {{ number_format($grandTotal, 2) }}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daily Summary Section -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Summary of Expenses of {{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}</h3>
            <a href="{{ route('costs.print.daily', ['date' => $selectedDate]) }}" 
               target="_blank" 
               class="btn btn-light btn-sm">
                <i class="bi bi-printer"></i> Print Daily Report
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Date Selector -->
        <form action="{{ route('costs.index') }}" method="GET" class="mb-3">
            <div class="form-group row">
                <label for="date" class="col-form-label col-sm-2">Select Date</label>
                <div class="col-sm-4">
                    <input type="date" name="date" id="date" class="form-control" value="{{ $selectedDate }}">
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                </div>
            </div>
        </form>

        @if ($dailyGroupedCosts->isEmpty())
            <div class="alert alert-info">No expenses found for the selected date.</div>
        @else
            @foreach ($dailyGroupedCosts as $group => $persons)
                <h5 class="mt-4"><strong>{{ $group }}</strong></h5>
                <table class="table table-bordered table-hover mb-4">
                    <thead class="table-light">
                        <tr>
                            <th>Person/Shop</th>
                            <th>Description</th>
                            <th>Expense</th>
                            <th>Created Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($persons as $person => $data)
                            <tr class="table-secondary">
                                <td colspan="5"><strong>{{ $person }}</strong></td>
                            </tr>
                            @foreach ($data['costs'] as $cost)
                                <tr>
                                    <td>{{ $person }}</td>
                                    <td>{{ $cost->description ?? '-' }}</td>
                                    <td>Rs. {{ number_format($cost->amount, 2) }}</td>
                                    <td>{{ $cost->created_at->format('h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('costs.print.transaction', $cost) }}" 
                                           target="_blank"
                                           class="btn btn-outline-info btn-sm">
                                            <i class="bi bi-printer"></i> Print
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="table-info">
                                <td class="text-end"><strong>Total for {{ $person }}</strong></td>
                                <td colspan="4"><strong>Rs. {{ number_format($data['total'], 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        @endif
    </div>
</div>

    <!-- Log Details Section -->
<div class="card">
    <div class="card-header bg-info text-white">
        <h3 class="mb-0">Stock Log Details Daily</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Category</th>
                    <th>Person/Shop</th>
                    <th>Expense</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logDetails as $log)
                    <tr>
                        <td>{{ $log['date'] ? $log['date']->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $log['user'] }}</td>
                        <td>{{ $log['category'] }}</td>
                        <td>{{ $log['person_shop'] }}</td>
                        <td>Rs. {{ number_format($log['expense'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No log details available for the selected date.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    
    
    
    // Debug logs to check if Chart.js is loaded
    console.log('Chart.js loaded:', typeof Chart !== 'undefined');
    
    // Debug logs to check canvas elements
    console.log('Category Chart Canvas:', document.getElementById('categoryChart'));
    console.log('Daily Chart Canvas:', document.getElementById('dailyChart'));

    // Debug logs for chart data
    console.log('Category Distribution Data:', {
        labels: {!! json_encode($chartData['categoryDistribution']->pluck('category')) !!},
        data: {!! json_encode($chartData['categoryDistribution']->pluck('total')) !!}
    });
    @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Charts
    initializeCharts();
    
    // Initialize Collapse Functionality
    initializeCollapse();
});

function initializeCharts() {
    try {
        // Category Distribution Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['categoryDistribution']->pluck('category')) !!},
                datasets: [{
                    data: {!! json_encode($chartData['categoryDistribution']->pluck('total')) !!},
                    backgroundColor: [
                        '#4F46E5', '#7C3AED', '#EC4899', '#EF4444', '#F59E0B',
                        '#10B981', '#3B82F6', '#6366F1', '#8B5CF6', '#D946EF'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Expense Distribution by Category'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `Rs. ${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Daily Expenses Chart
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['dailyExpenses']->pluck('date')) !!},
                datasets: [{
                    label: 'Daily Expenses',
                    data: {!! json_encode($chartData['dailyExpenses']->pluck('total')) !!},
                    backgroundColor: '#3B82F6',
                    borderColor: '#2563EB',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs. ' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Daily Expenses Trend'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rs. ' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error initializing charts:', error);
    }
}

function initializeCollapse() {
    document.querySelectorAll('.clickable').forEach(function(element) {
        element.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            const icon = this.querySelector('.float-right');
            const isCollapsed = this.classList.contains('collapsed');
            
            // Update icon
            icon.innerHTML = isCollapsed ? '▲' : '▼';
            
            // Toggle collapse state
            this.classList.toggle('collapsed');
            
            // Toggle target elements
            document.querySelectorAll(target).forEach(function(targetElement) {
                targetElement.classList.toggle('show');
            });
        });
    });
}
</script>
@endpush

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 1rem;
}

.clickable {
    cursor: pointer;
}

.clickable:hover {
    background-color: #f8f9fa;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}

.chart-container {
    position: relative;
    margin: auto;
    height: 300px;
}

.collapse {
    display: none;
}

.collapse.show {
    display: table-row;
}

.float-right {
    float: right;
    transition: transform 0.2s;
}

.collapsed .float-right {
    transform: rotate(180deg);
}
</style>
