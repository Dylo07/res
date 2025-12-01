@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Monthly Salary Processing</h3>
            <a href="{{ route('salary.basic') }}" class="btn btn-outline-light">
                Assign Basic Salary
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Month</label>
                        <select id="month" class="form-control">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Year</label>
                        <select id="year" class="form-control">
                            @foreach(range(date('Y')-2, date('Y')) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Employee</th>
                            <th>Basic Salary</th>
                            <th>Salary Advance</th>
                            <th>Present Days</th>
                            <th>Absent Days</th>
                            <th>Final Salary</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($staff as $employee)
                    @php
    $attendance = $attendanceData[$employee->id] ?? ['present' => 0, 'half' => 0, 'absent' => 0];
    $salaryAdvance = $salaryAdvances->where('person.id', $employee->id)->sum('amount');
    $finalSalary = 0;
    
    // Get the current date
    $currentDate = \Carbon\Carbon::now();
    $lastDayOfMonth = \Carbon\Carbon::create($year, $month)->endOfMonth()->day;
    $totalMarkedDays = $attendance['present'] + $attendance['half'] + $attendance['absent'];
    
    // Calculate present and absent days
    $presentDays = $attendance['present'] + ($attendance['half'] * 0.5);
    $absentDays = $attendance['absent'];
    
    // Only show days if they've been marked
    $showAttendance = $totalMarkedDays > 0;
    
    if ($employee->basic_salary > 0) {
        // If month is not complete or not fully marked
        if ($totalMarkedDays < $lastDayOfMonth) {
            // Calculate based on marked present days only
            $finalSalary = ($presentDays * $employee->basic_salary / 30) - $salaryAdvance;
        } else {
            $totalDaysOff = $absentDays + ($attendance['half'] * 0.5);
            
            if ($totalDaysOff == 5) {
                $finalSalary = $employee->basic_salary - $salaryAdvance;
            } elseif ($totalDaysOff < 5) {
                $additionalDays = 5 - $totalDaysOff;
                $dailyRate = $employee->basic_salary / 30;
                $finalSalary = $employee->basic_salary - $salaryAdvance + ($additionalDays * $dailyRate);
            } else {
                $excessDays = $totalDaysOff - 5;
                $dailyRate = $employee->basic_salary / 25;
                $finalSalary = $employee->basic_salary - $salaryAdvance - ($excessDays * $dailyRate);
            }
        }
    }
@endphp
<tr>
    <td>{{ $employee->name }}</td>
    <td class="text-end">Rs. {{ number_format($employee->basic_salary ?? 0, 2) }}</td>
    <td class="text-end">Rs. {{ number_format($salaryAdvance, 2) }}</td>
    <td class="text-center">{{ $showAttendance ? $presentDays : '-' }}</td>
    <td class="text-center">{{ $showAttendance ? $absentDays : '-' }}</td>
    <td class="text-end">Rs. {{ number_format($finalSalary, 2) }}</td>
    <td class="text-center">
    @if($employee->basic_salary > 0)
        <a href="{{ route('salary.generatePayslip', [
            'person_id' => $employee->id, 
            'month' => $month, 
            'year' => $year
        ]) }}" class="btn btn-info btn-sm">Generate Payslip</a>
    @else
            -
        @endif
    </td>
</tr>
@endforeach
                    </tbody>
                </table>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center gap-3">
                                <h5 class="mb-0">Salary Advances</h5>
                                <select class="form-select form-select-sm bg-dark text-white border-secondary" 
                                        style="width: auto; min-width: 200px;"
                                        id="advancePeriodSelect">
                                    @foreach($periods as $index => $period)
                                        <option value="{{ $index }}" {{ $selectedPeriod == $index ? 'selected' : '' }}>
                                            {{ $period['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0 me-3 text-warning">Total: Rs. {{ number_format($totalAdvance, 2) }}</h6>
                            <a href="{{ route('costs.create') }}" class="btn btn-sm btn-outline-light">Add Advance</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Employee Name</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-center">Number of Advances</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salaryAdvances->groupBy('person.name') as $employeeName => $advances)
                                    <tr>
                                        <td><strong>{{ $employeeName }}</strong></td>
                                        <td class="text-end text-danger fw-bold">Rs. {{ number_format($advances->sum('amount'), 2) }}</td>
                                        <td class="text-center">{{ $advances->count() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No salary advances found for this period
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td class="text-end text-warning"><strong>Rs. {{ number_format($totalAdvance, 2) }}</strong></td>
                                    <td class="text-center"><strong>{{ $salaryAdvances->count() }}</strong></td>
                                </tr>
                            </tfoot>
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
    $('#month, #year').change(function() {
        window.location.href = '{{ route("salary.index") }}?month=' + $('#month').val() + '&year=' + $('#year').val();
    });

    $('#advancePeriodSelect').change(function() {
        window.location.href = window.location.href.split('?')[0] + '?period=' + this.value;
    });
});
</script>
@endpush

<style>
.text-end {
    text-align: right;
}
.text-center {
    text-align: center;
}
.bg-dark {
    background-color: #343a40;
}
.text-white {
    color: #fff;
}
</style>
@endsection