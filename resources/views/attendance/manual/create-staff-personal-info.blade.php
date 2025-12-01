@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus"></i> Add New Staff Member
                </h4>
                <div>
                    <a href="{{ route('staff.information') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Staff List
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('staff.personal.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Personal Information Section -->
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-user"></i> Personal Information
                        </h5>

                        <div class="form-group">
                            <label for="name">Display Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" required>
                            <small class="form-text text-muted">This is the name that will appear in the system</small>
                        </div>

                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="{{ old('full_name') }}">
                        </div>

                        <div class="form-group">
                            <label for="id_card_number">ID Card Number</label>
                            <input type="text" class="form-control" id="id_card_number" name="id_card_number" 
                                   value="{{ old('id_card_number') }}">
                        </div>

                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                   value="{{ old('date_of_birth') }}">
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="blood_group">Blood Group</label>
                            <select class="form-control" id="blood_group" name="blood_group">
                                <option value="">Select Blood Group</option>
                                <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="col-md-6">
                        <h5 class="text-success mb-3">
                            <i class="fas fa-phone"></i> Contact Information
                        </h5>

                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                   value="{{ old('phone_number') }}">
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email') }}">
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="emergency_contact">Emergency Contact Name</label>
                            <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" 
                                   value="{{ old('emergency_contact') }}">
                        </div>

                        <div class="form-group">
                            <label for="emergency_phone">Emergency Contact Phone</label>
                            <input type="tel" class="form-control" id="emergency_phone" name="emergency_phone" 
                                   value="{{ old('emergency_phone') }}">
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Employment Information Section -->
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-warning mb-3">
                            <i class="fas fa-briefcase"></i> Employment Information
                        </h5>

                        <div class="form-group">
                            <label for="staff_code">Staff Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="staff_code" name="staff_code" 
                                   value="{{ old('staff_code') }}" required>
                            <small class="form-text text-muted">Unique identifier for this staff member (e.g., EMP001)</small>
                        </div>

                        <div class="form-group">
                            <label for="staff_category">Staff Category <span class="text-danger">*</span></label>
                            <select class="form-control" id="staff_category" name="staff_category" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $value)
                                    <option value="{{ $key }}" {{ old('staff_category') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="text" class="form-control" id="position" name="position" 
                                   value="{{ old('position') }}">
                        </div>

                        <div class="form-group">
                            <label for="hire_date">Hire Date</label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                   value="{{ old('hire_date') }}">
                        </div>

                        <div class="form-group">
                            <label for="basic_salary">Basic Salary (Rs.)</label>
                            <input type="number" class="form-control" id="basic_salary" name="basic_salary" 
                                   min="0" step="0.01" value="{{ old('basic_salary') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="text-info mb-3">
                            <i class="fas fa-sticky-note"></i> Additional Information
                        </h5>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="8" 
                                      placeholder="Any additional notes about this staff member...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> Fields marked with <span class="text-danger">*</span> are required. 
                            Other fields can be filled in later by editing the staff member's profile.
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Create Staff Member
                            </button>
                            <a href="{{ route('staff.information') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-control {
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .btn {
        border-radius: 0.25rem;
    }
    
    .form-text {
        color: #6c757d;
        font-size: 0.875em;
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        
        .form-group {
            margin-bottom: 0.75rem;
        }
    }
</style>
@endpush
@endsection
