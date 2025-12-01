

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-lock"></i> Staff Information Access
                    </h4>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h5 class="text-muted">Enter Password to Access Staff Information</h5>
                        <p class="text-muted">This section contains sensitive staff personal information and requires special access.</p>
                    </div>

                    {{-- UPDATED: Submit to staff.information route specifically --}}
                    <form method="POST" action="{{ route('staff.information') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="staff_password" class="form-label fw-bold">
                                <i class="fas fa-key"></i> Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control form-control-lg" 
                                       id="staff_password" 
                                       name="staff_password" 
                                       placeholder="Enter staff section password"
                                       required 
                                       autofocus>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggle-icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-unlock"></i> Access Staff Information
                            </button>
                        </div>

                        <div class="text-center">
                            <small class="text-muted">
                                Default password: <code>****</code>
                            </small>
                        </div>
                    </form>
                </div>

                <div class="card-footer bg-light text-center">
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card { border: none; border-radius: 15px; }
    .card-header { border-radius: 15px 15px 0 0; }
    .form-control-lg { border-radius: 10px; }
    .btn-lg { border-radius: 10px; padding: 12px; }
    .input-group-text { background-color: #f8f9fa; border-color: #ced4da; }
    .alert { border-radius: 10px; }
    .text-primary { color: #007bff !important; }
    .shadow { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
    body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding-top: 2rem; }
    .container { margin-top: 2rem; }
</style>
@endpush

@push('scripts')
<script>
function togglePassword() {
    const passwordField = document.getElementById('staff_password');
    const toggleIcon = document.getElementById('toggle-icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('staff_password').focus();
});
</script>
@endpush
@endsection
