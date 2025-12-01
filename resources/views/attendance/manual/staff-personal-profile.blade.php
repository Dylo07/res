@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <i class="fas fa-user"></i> {{ $person->name }}
        </h4>
        <div>
            {{-- UPDATED: Everyone can now edit and print --}}
            <a href="{{ route('staff.personal.edit', $person->id) }}" class="btn btn-success btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('staff.print.id.card', $person->id) }}" class="btn btn-info btn-sm" target="_blank">
                <i class="fas fa-print"></i> Print ID Card
            </a>
            <form method="POST" action="{{ route('staff.section.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm" title="Logout from Staff Section">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
            <a href="{{ route('staff.information') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Personal Information Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-id-card"></i> Personal Information
                            </h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Person ID:</th>
                                    <td><span class="badge badge-primary">{{ $person->id }}</span></td>
                                </tr>
                                <tr>
                                    <th>Full Name:</th>
                                    <td>{{ $person->full_name ?? $person->name }}</td>
                                </tr>
                                <tr>
                                    <th>ID Card Number:</th>
                                    <td>{{ $person->id_card_number ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth:</th>
                                    <td>
                                        @if($person->date_of_birth)
                                            {{ \Carbon\Carbon::parse($person->date_of_birth)->format('F j, Y') }}
                                            <small class="text-muted">({{ $age }} years old)</small>
                                        @else
                                            Not provided
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Gender:</th>
                                    <td>{{ $person->gender ? ucfirst($person->gender) : 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Blood Group:</th>
                                    <td>{{ $person->blood_group ?? 'Not provided' }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-success mb-3">
                                <i class="fas fa-phone"></i> Contact Information
                            </h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Phone Number:</th>
                                    <td>
                                        @if($person->phone_number)
                                            <a href="tel:{{ $person->phone_number }}" class="text-primary">
                                                {{ $person->phone_number }}
                                            </a>
                                        @else
                                            Not provided
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>
                                        @if($person->email)
                                            <a href="mailto:{{ $person->email }}" class="text-primary">
                                                {{ $person->email }}
                                            </a>
                                        @else
                                            Not provided
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>{{ $person->address ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Emergency Contact:</th>
                                    <td>{{ $person->emergency_contact ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Emergency Phone:</th>
                                    <td>
                                        @if($person->emergency_phone)
                                            <a href="tel:{{ $person->emergency_phone }}" class="text-primary">
                                                {{ $person->emergency_phone }}
                                            </a>
                                        @else
                                            Not provided
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Employment Information Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-warning mb-3">
                                <i class="fas fa-briefcase"></i> Employment Information
                            </h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Staff Code:</th>
                                    <td>
                                        @if($person->staffCode)
                                            <span class="badge badge-info">{{ $person->staffCode->staff_code }}</span>
                                        @else
                                            Not assigned
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>
                                        @if($person->staffCategory)
                                            <span class="badge badge-secondary">
                                                {{ ucfirst(str_replace('_', ' ', $person->staffCategory->category)) }}
                                            </span>
                                        @else
                                            Not assigned
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Position:</th>
                                    <td>{{ $person->position ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Hire Date:</th>
                                    <td>
                                        @if($person->hire_date)
                                            {{ \Carbon\Carbon::parse($person->hire_date)->format('F j, Y') }}
                                            <small class="text-muted">({{ $yearsOfService }} years of service)</small>
                                        @else
                                            Not provided
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Basic Salary:</th>
                                    <td>
                                        @if($person->basic_salary)
                                            Rs. {{ number_format($person->basic_salary, 2) }}
                                        @else
                                            Not specified
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-info mb-3">
                                <i class="fas fa-calendar-check"></i> Recent Attendance (Last 30 Days)
                            </h5>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="card bg-success text-white text-center">
                                        <div class="card-body py-2">
                                            <h4 class="mb-0">{{ $attendanceSummary['present_days'] }}</h4>
                                            <small>Present</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-warning text-white text-center">
                                        <div class="card-body py-2">
                                            <h4 class="mb-0">{{ $attendanceSummary['half_days'] }}</h4>
                                            <small>Half Days</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-2">
                                <div class="col-6">
                                    <div class="card bg-danger text-white text-center">
                                        <div class="card-body py-2">
                                            <h4 class="mb-0">{{ $attendanceSummary['absent_days'] }}</h4>
                                            <small>Absent</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-primary text-white text-center">
                                        <div class="card-body py-2">
                                            <h4 class="mb-0">{{ $attendanceSummary['attendance_rate'] }}%</h4>
                                            <small>Rate</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($person->notes)
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-secondary mb-3">
                                    <i class="fas fa-sticky-note"></i> Notes
                                </h5>
                                <div class="alert alert-light">
                                    {{ $person->notes }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Quick Actions Card -->
           <div class="card mb-3">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-tools"></i> Quick Actions
        </h6>
    </div>
    <div class="card-body">
        <div class="d-grid gap-2">
            <a href="{{ route('attendance.manual.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-calendar-check"></i> Mark Attendance
            </a>
            {{-- UPDATED: Everyone can now edit --}}
            <a href="{{ route('staff.personal.edit', $person->id) }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-edit"></i> Edit Information
            </a>
            <a href="{{ route('staff.print.id.card', $person->id) }}" class="btn btn-outline-info btn-sm" target="_blank">
                <i class="fas fa-print"></i> Print ID Card
            </a>
            <a href="tel:{{ $person->phone_number }}" class="btn btn-outline-warning btn-sm" {{ !$person->phone_number ? 'disabled' : '' }}>
                <i class="fas fa-phone"></i> Call
            </a>
            <a href="mailto:{{ $person->email }}" class="btn btn-outline-secondary btn-sm" {{ !$person->email ? 'disabled' : '' }}>
                <i class="fas fa-envelope"></i> Email
            </a>
            <hr>
            <form method="POST" action="{{ route('staff.section.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                    <i class="fas fa-sign-out-alt"></i> Logout from Staff Section
                </button>
            </form>
        </div>
    </div>
</div>
{{-- Update the Profile Completeness card: --}}

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-chart-pie"></i> Profile Completeness
        </h6>
    </div>
    <div class="card-body">
        @php
            $fields = [
                'full_name', 'id_card_number', 'phone_number', 'email', 'address',
                'date_of_birth', 'gender', 'position', 'hire_date', 'emergency_contact'
            ];
            $completed = 0;
            foreach($fields as $field) {
                if($person->$field) $completed++;
            }
            $percentage = round(($completed / count($fields)) * 100);
        @endphp
        
        <div class="progress mb-2" style="height: 20px;">
            <div class="progress-bar 
                @if($percentage >= 80) bg-success
                @elseif($percentage >= 60) bg-warning
                @else bg-danger
                @endif" 
                role="progressbar" 
                style="width: {{ $percentage }}%">
                {{ $percentage }}%
            </div>
        </div>
        
        <small class="text-muted">
            {{ $completed }} of {{ count($fields) }} fields completed
        </small>
        
        {{-- UPDATED: Everyone can now complete profile --}}
        @if($percentage < 100)
            <div class="mt-2">
                <a href="{{ route('staff.personal.edit', $person->id) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Complete Profile
                </a>
            </div>
        @endif
    </div>
</div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        border: none;
        padding: 0.5rem 0.75rem;
        font-weight: 600;
        color: #495057;
    }
    
    .table td {
        border: none;
        padding: 0.5rem 0.75rem;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .progress {
        background-color: #e9ecef;
    }
    
    .d-grid {
        display: grid;
        gap: 0.5rem;
    }
    
    .text-primary {
        text-decoration: none;
    }
    
    .text-primary:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
    }
</style>
@endpush
@endsection