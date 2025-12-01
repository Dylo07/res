@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Cashier Balance Dashboard</h3>
                    <form action="{{ route('cashier.balance') }}" method="GET" class="form-inline">
                        <div class="input-group">
                            <input type="date" name="date" class="form-control" 
                                   value="{{ $selectedDate->format('Y-m-d') }}"
                                   max="{{ now()->format('Y-m-d') }}">
                            <button type="submit" class="btn btn-light ml-2">View Date</button>
                            @if($selectedDate->format('Y-m-d') !== now()->format('Y-m-d'))
                                <a href="{{ route('cashier.balance') }}" class="btn btn-warning ml-2">
                                    Back to Today
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($selectedDate->format('Y-m-d') !== now()->format('Y-m-d'))
                        <div class="alert alert-info">
                            Viewing balance for: {{ $selectedDate->format('d M Y') }}
                        </div>
                    @endif

                    <!-- Opening Balance Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    Opening Balance ({{ $selectedDate->format('d M Y') }})
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('cashier.update-opening-balance') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                                        <div class="input-group">
                                            <input type="number" step="0.01" name="opening_balance" 
                                                   class="form-control" 
                                                   value="{{ $currentBalance->opening_balance }}"
                                                   required>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    Previous Day Closing Balance
                                </div>
                                <div class="card-body">
                                    <h3>Rs. {{ number_format($previousBalance ? $previousBalance->closing_balance : 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Balance Summary Section -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        Balance Summary for {{ $selectedDate->format('d M Y') }}
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Opening Balance</h5>
                        <h4>Rs. {{ number_format($currentBalance->opening_balance, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today's Sales</h5>
                        <h4>Rs. {{ number_format($currentBalance->total_sales, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Today's Expenses</h5>
                        <h4>Rs. {{ number_format($currentBalance->total_expenses + $currentBalance->manual_expenses, 2) }}</h4>
                        <small>Regular: Rs. {{ number_format($currentBalance->total_expenses, 2) }}</small><br>
                        <small>Manual: Rs. {{ number_format($currentBalance->manual_expenses, 2) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Current Balance</h5>
                        <h4>Rs. {{ number_format($currentBalance->closing_balance, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daily Summary Alert -->
<div class="alert alert-success mb-4" role="alert">
    <h5>Daily Summary {{ $selectedDate->format('d M Y') }}</h5>
    <p class="mb-1">Total Sales: Rs. {{ number_format($currentBalance->total_sales, 2) }}</p>
    <p class="mb-1">Total Expenses: Rs. {{ number_format($currentBalance->total_expenses + $currentBalance->manual_expenses, 2) }}</p>
    <p class="mb-0">Net Balance: Rs. {{ number_format($currentBalance->closing_balance, 2) }}</p>
</div>

                    <!-- Manual Transaction Form -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning">
                            Add Manual Transaction
                        </div>
                        <div class="card-body">
                            <form action="{{ route('cashier.add-manual-transaction') }}" method="POST">
                                @csrf
                                <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input type="number" step="0.01" name="amount" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Type</label>
                                            <select name="type" class="form-control" required>
                                                <option value="earning">Additional Earning</option>
                                                <option value="expense">Manual Expense</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Notes</label>
                                            <input type="text" name="notes" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-warning form-control">Add</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Manual Transactions Table -->
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            Manual Transactions for {{ $selectedDate->format('d M Y') }}
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Notes</th>
                                            <th>Added By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($manualTransactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->created_at->format('h:i A') }}</td>
                                                <td>
                                                    @if($transaction->type === 'earning')
                                                        <span class="badge bg-success">Additional Earning</span>
                                                    @else
                                                        <span class="badge bg-danger">Manual Expense</span>
                                                    @endif
                                                </td>
                                                <td>Rs. {{ number_format($transaction->amount, 2) }}</td>
                                                <td>{{ $transaction->notes }}</td>
                                                <td>{{ $transaction->createdBy->name ?? 'System' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No manual transactions for this date</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if($manualTransactions->count() > 0)
                                        <tfoot>
                                            <tr class="bg-light">
                                                <td colspan="2"><strong>Totals</strong></td>
                                                <td colspan="3">
                                                    <div>Additional Earnings: Rs. {{ number_format($manualTransactions->where('type', 'earning')->sum('amount'), 2) }}</div>
                                                    <div>Manual Expenses: Rs. {{ number_format($manualTransactions->where('type', 'expense')->sum('amount'), 2) }}</div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Date Navigation -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('cashier.balance', ['date' => $selectedDate->copy()->subDay()->format('Y-m-d')]) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-chevron-left"></i> Previous Day
                                </a>
                                
                                @if($selectedDate->format('Y-m-d') < now()->format('Y-m-d'))
                                    <a href="{{ route('cashier.balance', ['date' => $selectedDate->copy()->addDay()->format('Y-m-d')]) }}" 
                                       class="btn btn-outline-primary">
                                        Next Day <i class="fas fa-chevron-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.75em;
    }
    .table td {
        vertical-align: middle;
    }
    .input-group .btn {
        margin-left: 5px;
    }
    .card {
        margin-bottom: 1rem;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .alert {
        margin-bottom: 1rem;
    }
    .small-text {
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevent future dates from being selected
    const dateInput = document.querySelector('input[type="date"]');
    if (dateInput) {
        dateInput.max = new Date().toISOString().split('T')[0];
    }

    // Format numbers in input fields
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
});
</script>
@endpush

@endsection