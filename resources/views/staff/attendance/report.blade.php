@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Attendance Report
            <form class="float-right form-inline">
            <input type="date" name="start_date" class="form-control mr-2" value="{{ $startDate->format('Y-m-d') }}">
            <input type="date" name="end_date" class="form-control mr-2" value="{{ $endDate->format('Y-m-d') }}">
            <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Total Days</th>
                            <th>Present</th>
                            <th>Late</th>
                            <th>Absent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $staffId => $data)
                        <tr>
                            <td>{{ $data['name'] }}</td>
                            <td>{{ $data['total_days'] }}</td>
                            <td class="text-success">{{ $data['present'] }}</td>
                            <td class="text-warning">{{ $data['late'] }}</td>
                            <td class="text-danger">{{ $data['absent'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No attendance records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection