@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Attendance Report</h3>
                <div>
                    <a href="{{ route('attendance.manual.index') }}" class="btn btn-primary">Back to Attendance</a>
                    <button onclick="window.print()" class="btn btn-secondary">Print Report</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Filter Form -->
            <form action="{{ route('attendance.manual.report') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Staff Member</label>
                            <select name="staff_member" class="form-control">
                                <option value="">All Staff</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}" {{ request('staff_member') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Staff Category</label>
                            <select name="staff_category" class="form-control">
                                <option value="">All Categories</option>
                                @foreach($categories as $value => $name)
                                    <option value="{{ $value }}" {{ request('staff_category') == $value ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Filter Report</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5>Present Days</h5>
                            <h2>{{ $summary['total_present'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5>Half Days</h5>
                            <h2>{{ $summary['total_half'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h5>Absent Days</h5>
                            <h2>{{ $summary['total_absent'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5>Total Records</h5>
                            <h2>{{ array_sum($summary) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th class="align-middle">Date</th>
                            @foreach($staff as $member)
                                <th class="align-middle text-center">
                                    {{ $member->name }}
                                    @if($member->staffCategory)
                                        <small class="d-block text-muted">{{ ucfirst(str_replace('_', ' ', $member->staffCategory->category)) }}</small>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dates as $date)
                            <tr>
                                <td class="align-middle">{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</td>
                                @foreach($staff as $member)
                                    <td class="text-center align-middle">
                                        @php
                                            $attendance = $attendanceMap[$date][$member->id] ?? null;
                                            $statusClass = !$attendance ? 'secondary' : 
                                                ($attendance->status === 'present' ? 'success' : 
                                                ($attendance->status === 'half' ? 'warning' : 'danger'));
                                            $statusText = !$attendance ? 'Not Marked' : ucfirst($attendance->status);
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                        @if($attendance && $attendance->remarks)
                                            <small class="d-block text-muted mt-1">{{ $attendance->remarks }}</small>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .badge {
        font-size: 85%;
        padding: 5px 10px;
    }
    .table-sm td, .table-sm th {
        padding: 0.5rem;
        vertical-align: middle;
    }
    @media print {
        .btn, form { display: none !important; }
        .card { border: none !important; }
    }
</style>
@endpush
@endsection