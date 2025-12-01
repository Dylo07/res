@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Add Staff Member</h3>
                <div>
                    <a href="{{ route('attendance.manual.index') }}" class="btn btn-secondary">Back to Attendance</a>
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('attendance.manual.add-staff') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="person_id">Select Person:</label>
                    <select name="person_id" id="person_id" class="form-control" required>
                        <option value="">-- Select Person --</option>
                        @foreach($availablePersons as $person)
                            <option value="{{ $person->id }}">{{ $person->id }} - {{ $person->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="staff_code">Staff Code:</label>
                    <input type="text" class="form-control" id="staff_code" name="staff_code" 
                           placeholder="e.g. EMP001" required>
                </div>
                
                <div class="form-group">
                    <label for="staff_category">Staff Category:</label>
                    <select name="staff_category" id="staff_category" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <option value="front_office">Front Office</option>
                        <option value="garden">Garden</option>
                        <option value="kitchen">Kitchen</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="restaurant">Restaurant</option>
                        <option value="housekeeping">Housekeeping</option>
                        <option value="pool">Pool</option>
                        <option value="laundry">Laundry</option>
                    </select>
                </div>
                
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                
                <button type="submit" class="btn btn-primary">Add Staff Member</button>
            </form>
        </div>
    </div>
</div>
@endsection