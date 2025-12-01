@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-black text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Quotations</h5>
                <a href="{{ route('quotations.create') }}" class="btn btn-sm btn-outline-light">
                    Create New Quotation
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Client Name</th>
                            <th>Client Address</th>
                            <th>Date</th>
                            <th>Schedule</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $quotation)
                            <tr>
                                <td>{{ $quotation->id }}</td>
                                <td>{{ $quotation->client_name }}</td>
                                <td>{{ $quotation->client_address }}</td>
                                <td>{{ $quotation->quotation_date->format('Y-m-d') }}</td>
                                <td>{{ $quotation->schedule->format('Y-m-d') }}</td>
                                <td class="text-end">Rs. {{ number_format($quotation->total_amount, 2) }}</td>
                                <td>
                                    @if($quotation->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($quotation->status === 'converted')
                                        <span class="badge bg-success">Converted</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($quotation->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('quotations.show', $quotation) }}" 
                                           class="btn btn-info">
                                            View
                                        </a>
                                        
                                        <a href="{{ route('quotations.print', $quotation) }}" 
                                           class="btn btn-success">
                                            Print
                                        </a>
                                        @if($quotation->status === 'pending')
                                            <form action="{{ route('quotations.convert-to-booking', $quotation) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No quotations found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.bg-black {
    background-color: #000000;
}

.table-dark {
    background-color: #000000;
}

.btn-group .btn {
    margin-right: 2px;
}

.badge {
    font-size: 0.875em;
    padding: 0.5em 0.75em;
}
</style>
@endsection