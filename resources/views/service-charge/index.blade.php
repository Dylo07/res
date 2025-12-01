@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Service Charge Processing</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('service-charge.points') }}" class="btn btn-light btn-sm">
                                Manage Points
                            </a>
                            <select id="month" class="form-select form-select-sm bg-dark text-white border-secondary" 
                                    style="width: auto; min-width: 200px;">
                                @foreach($months as $value => $label)
                                    <option value="{{ $value }}" {{ $month == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Previous Month's Total S/C</h6>
                                    <h4>Rs. {{ number_format($prevMonthSales, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Current Month's Damages</h6>
                                    <h4>Rs. {{ number_format($currentMonthDamages, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Total Balance S/C</h6>
                                    <h4>Rs. {{ number_format($totalGivingSC, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Employee</th>
                                    <th>Points</th>
                                    <th>Ratio</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staff as $employee)
                                @php
                                    $employeePoints = $points->where('person_id', $employee->id)->first();
                                    $pointValue = $employeePoints ? $employeePoints->points : 0;
                                    $ratio = $totalPoints > 0 ? ($pointValue / $totalPoints) : 0;
                                    $amount = $totalGivingSC * $ratio;
                                @endphp
                                <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $pointValue }}</td>
                                    <td>{{ number_format($ratio * 100, 2) }}%</td>
                                    <td>Rs. {{ number_format($amount, 2) }}</td>
                                    <td>
                                        @if(!$serviceCharges->where('person_id', $employee->id)->first())
                                            <button class="btn btn-primary btn-sm generate-sc"
                                                    data-person="{{ $employee->id }}">
                                                Generate S/C
                                            </button>
                                        @else
                                            <a href="{{ route('service-charge.print', $serviceCharges->where('person_id', $employee->id)->first()->id) }}"
                                               class="btn btn-info btn-sm">
                                                Print
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Generate service charge
    $('.generate-sc').click(function() {
        const personId = $(this).data('person');
        const month = $('#month').val();

        $.ajax({
            url: '{{ route("service-charge.generate") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                person_id: personId,
                month: month
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });

    // Month change
    $('#month').change(function() {
        window.location.href = '{{ route("service-charge.index") }}?month=' + $(this).val();
    });
});
</script>
@endpush
@endsection