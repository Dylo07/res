{{-- Replace your entire staff-information.blade.php file with this corrected version --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-id-card"></i> Staff Personal Information
                    <small class="text-muted">({{ $staff->count() }} total)</small>
                </h3>
                <div>
                    {{-- Everyone can now add staff and export --}}
                    <a href="{{ route('staff.personal.create') }}" class="btn btn-success mr-2">
                        <i class="fas fa-plus"></i> Add New Staff
                    </a>
                    <a href="{{ route('staff.information.export') }}" class="btn btn-primary mr-2">
                        <i class="fas fa-download"></i> Export
                    </a>
                    <form method="POST" action="{{ route('staff.section.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning mr-2" title="Logout from Staff Section">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                    <a href="{{ route('attendance.manual.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Attendance
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- Session Status Alert --}}
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> 
                <strong>Access Granted:</strong> You are now in the staff information section. 
                Logged in as <strong>{{ Auth::user()->name }}</strong> at {{ now()->format('M j, Y g:i A') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

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

            <!-- Filters Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="category_filter">Filter by Category:</label>
                    <select id="category_filter" class="form-control" onchange="filterData()">
                        <option value="all">All Categories</option>
                        @foreach($categories as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search_filter">Search:</label>
                    <input type="text" id="search_filter" class="form-control" placeholder="Name, ID Card, Phone..." onkeyup="filterData()">
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <div class="form-control-static">
                        <button type="button" class="btn btn-info" onclick="clearFilters()">
                            <i class="fas fa-refresh"></i> Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-0">{{ $staff->count() }}</h4>
                            <p class="mb-0">Total Staff</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-0">{{ $staff->where('staffCategory')->count() }}</h4>
                            <p class="mb-0">With Categories</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-0">{{ $staff->whereNotNull('phone_number')->count() }}</h4>
                            <p class="mb-0">With Phone</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-0">{{ $staff->whereNotNull('email')->count() }}</h4>
                            <p class="mb-0">With Email</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Information Cards -->
            <div class="row" id="staff-cards-container">
                @foreach($staff as $person)
                    <div class="col-md-6 col-lg-4 mb-4 staff-card" data-category="{{ $person->staffCategory ? $person->staffCategory->category : '' }}">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 font-weight-bold">{{ $person->name }}</h6>
                                    <span class="badge badge-primary">ID: {{ $person->id }}</span>
                                </div>
                                @if($person->staffCategory)
                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $person->staffCategory->category)) }}</small>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        @if($person->full_name)
                                            <p class="mb-2"><strong>Full Name:</strong> {{ $person->full_name }}</p>
                                        @endif
                                        
                                        @if($person->id_card_number)
                                            <p class="mb-2"><strong>ID Card:</strong> {{ $person->id_card_number }}</p>
                                        @endif
                                        
                                        @if($person->staffCode)
                                            <p class="mb-2"><strong>Staff Code:</strong> {{ $person->staffCode->staff_code }}</p>
                                        @endif
                                        
                                        @if($person->position)
                                            <p class="mb-2"><strong>Position:</strong> {{ $person->position }}</p>
                                        @endif
                                        
                                        @if($person->phone_number)
                                            <p class="mb-2">
                                                <strong>Phone:</strong> 
                                                <a href="tel:{{ $person->phone_number }}" class="text-primary">{{ $person->phone_number }}</a>
                                            </p>
                                        @endif
                                        
                                        @if($person->email)
                                            <p class="mb-2">
                                                <strong>Email:</strong> 
                                                <a href="mailto:{{ $person->email }}" class="text-primary">{{ $person->email }}</a>
                                            </p>
                                        @endif
                                        
                                        @if($person->address)
                                            <p class="mb-2"><strong>Address:</strong> {{ Str::limit($person->address, 50) }}</p>
                                        @endif
                                        
                                        @if($person->date_of_birth)
                                            <p class="mb-2">
                                                <strong>Age:</strong> {{ \Carbon\Carbon::parse($person->date_of_birth)->age }} years
                                            </p>
                                        @endif
                                        
                                        @if($person->hire_date)
                                            <p class="mb-2">
                                                <strong>Service:</strong> {{ \Carbon\Carbon::parse($person->hire_date)->diffInYears(\Carbon\Carbon::now()) }} years
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="btn-group btn-group-sm w-100" role="group">
                                    <a href="{{ route('staff.personal.profile', $person->id) }}" class="btn btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    {{-- Everyone can now edit --}}
                                    <a href="{{ route('staff.personal.edit', $person->id) }}" class="btn btn-success">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="{{ route('staff.print.id.card', $person->id) }}" class="btn btn-info" target="_blank">
                                        <i class="fas fa-print"></i> ID Card
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($staff->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No staff members found</h5>
                    <p class="text-muted">Start by adding some staff members to view their information here.</p>
                    {{-- Everyone can now add staff --}}
                    <a href="{{ route('staff.personal.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add First Staff Member
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
        border: 1px solid rgba(0,0,0,.125);
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,.1) !important;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
        background-color: #f8f9fa !important;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.775rem;
    }
    
    .staff-card {
        transition: opacity 0.3s;
    }
    
    .staff-card.hidden {
        opacity: 0.3;
        pointer-events: none;
    }
    
    .bg-primary, .bg-success, .bg-warning, .bg-info {
        border: none !important;
    }
    
    .text-primary {
        text-decoration: none;
    }
    
    .text-primary:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem 0.75rem;
        }
        
        .btn-group-sm > .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function filterData() {
    const categoryFilter = document.getElementById('category_filter').value;
    const searchFilter = document.getElementById('search_filter').value.toLowerCase();
    const staffCards = document.querySelectorAll('.staff-card');
    
    staffCards.forEach(card => {
        const category = card.getAttribute('data-category');
        const cardText = card.textContent.toLowerCase();
        
        let showCard = true;
        
        // Category filter
        if (categoryFilter !== 'all' && category !== categoryFilter) {
            showCard = false;
        }
        
        // Search filter
        if (searchFilter && !cardText.includes(searchFilter)) {
            showCard = false;
        }
        
        if (showCard) {
            card.style.display = 'block';
            card.classList.remove('hidden');
        } else {
            card.style.display = 'none';
            card.classList.add('hidden');
        }
    });
}

function clearFilters() {
    document.getElementById('category_filter').value = 'all';
    document.getElementById('search_filter').value = '';
    filterData();
}

// Auto-filter on page load if there are URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category');
    const search = urlParams.get('search');
    
    if (category) {
        document.getElementById('category_filter').value = category;
    }
    
    if (search) {
        document.getElementById('search_filter').value = search;
    }
    
    if (category || search) {
        filterData();
    }
});
</script>
@endpush
@endsection