@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-black text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Quotation Details #{{ $quotation->id }}</h5>
                <div>
                    <a href="{{ route('quotations.print', $quotation) }}" class="btn btn-sm btn-outline-light me-2">
                        Print
                    </a>
                    <a href="{{ route('quotations.index') }}" class="btn btn-sm btn-outline-light">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="mb-3">Client Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light" width="30%">Client Name</th>
                            <td>{{ $quotation->client_name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Client Address</th>
                            <td>{{ $quotation->client_address }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Quotation Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light" width="30%">Quotation Date</th>
                            <td>{{ $quotation->quotation_date->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Schedule Date</th>
                            <td>{{ $quotation->schedule->format('Y-m-d') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="mb-3">Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Price Per Item</th>
                                <th class="text-end">Pax</th>
                                <th class="text-end">Quantity</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotation->items as $item)
                            <tr>
                                <td>{{ $item['description'] }}</td>
                                <td class="text-end">{{ isset($item['pricePerItem']) ? number_format($item['pricePerItem'], 2) : '-' }}</td>
                                <td class="text-end">{{ isset($item['pax']) ? $item['pax'] : '-' }}</td>
                                <td class="text-end">{{ isset($item['quantity']) ? $item['quantity'] : '-' }}</td>
                                <td class="text-end">{{ isset($item['amount']) ? number_format($item['amount'], 2) : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Service Charge</th>
                                <td class="text-end">{{ number_format($quotation->service_charge, 2) }}</td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-end">Total Amount</th>
                                <td class="text-end fw-bold">{{ number_format($quotation->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="mb-3">Comments</h6>
                <ul class="list-group">
                    @foreach($quotation->comments as $comment)
                        <li class="list-group-item">{{ $comment }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="text-end">
                @if($quotation->status === 'pending')
                    <form action="{{ route('quotations.convert-to-booking', $quotation) }}" method="POST" class="d-inline">
                        @csrf
                        
                    </form>
                @endif
                
            </div>
        </div>
    </div>
</div>

<style>
.bg-black {
    background-color: #000000;
}

.table-light {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.875em;
    padding: 0.5em 0.75em;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endsection