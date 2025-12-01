@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Staff Manual Attendance</h3>
                <div>
                <a href="{{ route('attendance.manual.add-staff-form') }}" class="btn btn-primary me-2">
    <i class="fas fa-user-plus"></i> Add Staff Member
</a>
<a href="{{ route('attendance.manual.manage-categories') }}" class="btn btn-success me-2">
    <i class="fas fa-tags"></i> Manage Categories
</a>
<a href="{{ route('attendance.manual.report') }}" class="btn btn-info">View Report</a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div id="alert-container"></div>

            <!-- Add the session alerts here -->
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

            @if(Auth::user()->checkAdmin())
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i> As an admin, you can mark attendance for previous dates using the calendar icon.
                </div>
            @endif

            <!-- Category Filter -->
            <div class="mb-3">
                <div class="form-group">
                    <label for="category-filter">Filter by Category:</label>
                    <select id="category-filter" class="form-control">
                        <option value="all">All Categories</option>
                        <option value="front_office">Front Office</option>
                        <option value="kitchen">Kitchen</option>
                        <option value="restaurant">Restaurant</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="garden">Garden</option>
                        <option value="housekeeping">Housekeeping</option>
                        <option value="pool">Pool</option>
                        <option value="laundry">Laundry</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Person ID</th>
                            <th>Staff Name</th>
                            <th>Category</th>
                            <th>Attendance Status</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Define the category order
                            $displayOrder = ['front_office', 'kitchen', 'restaurant', 'maintenance', 'garden','laundry','pool','housekeeping', null];
                        @endphp
                        
                        @foreach($displayOrder as $category)
                            @if(isset($staffByCategory[$category]) && $staffByCategory[$category]->count() > 0)
                                <!-- Category Header -->
                                <tr class="category-header bg-light">
                                    <td colspan="6" class="font-weight-bold">
                                        {{ $categoryNames[$category] ?? 'Not Assigned' }} ({{ $staffByCategory[$category]->count() }})
                                    </td>
                                </tr>
                                
                                <!-- Staff in this category -->
                                @foreach($staffByCategory[$category] as $member)
                                    <tr id="staff-row-{{ $member->id }}" data-category="{{ $member->staffCategory ? $member->staffCategory->category : '' }}">
                                        <td>{{ $member->id }}</td>
                                        <td>
                                            {{ $member->name }}
                                            @if(Auth::user()->checkAdmin())
                                                <a href="#" onclick="openAttendanceHistory({{ $member->id }}, '{{ $member->name }}')" class="text-secondary ms-2" title="View Attendance History">
                                                    <i class="fas fa-history"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($member->staffCategory)
                                                <span class="badge badge-info">
                                                    {{ ucfirst(str_replace('_', ' ', $member->staffCategory->category)) }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td id="status-cell-{{ $member->id }}">
                                            @if(isset($attendances[$member->id]))
                                                <span class="badge badge-{{ $attendances[$member->id]->status == 'present' ? 'success' : ($attendances[$member->id]->status == 'half' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($attendances[$member->id]->status) }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Not Marked</span>
                                            @endif
                                        </td>
                                        <td id="remarks-cell-{{ $member->id }}">
                                            {{ isset($attendances[$member->id]) ? $attendances[$member->id]->remarks : '-' }}
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                @if(Auth::user()->checkAdmin())
                                                    <div class="input-group-prepend">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                                onclick="openAttendanceCalendar({{ $member->id }}, '{{ $member->name }}')" 
                                                                title="Mark attendance for previous dates">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </button>
                                                    </div>
                                                    <input type="hidden" 
                                                        id="date-{{ $member->id }}" 
                                                        value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                                                @else
                                                    <input type="hidden" 
                                                        id="date-{{ $member->id }}" 
                                                        value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
                                                @endif
                                                <input type="text" class="form-control form-control-sm remarks-input" 
                                                    id="remarks-{{ $member->id }}" placeholder="Remarks">
                                                <div class="input-group-append">
                                                    <button type="button" onclick="markAttendance({{ $member->id }}, 'present')" 
                                                        class="btn btn-sm btn-success">Present</button>
                                                    <button type="button" onclick="markAttendance({{ $member->id }}, 'half')" 
                                                        class="btn btn-sm btn-warning">Half Day</button>
                                                    <button type="button" onclick="markAttendance({{ $member->id }}, 'absent')" 
                                                        class="btn btn-sm btn-danger">Absent</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Calendar Modal -->
<div class="modal fade" id="attendanceCalendarModal" tabindex="-1" role="dialog" aria-labelledby="attendanceCalendarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceCalendarModalLabel">Attendance for <span id="modal-staff-name"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-person-id">
                
                <div class="mb-3">
                    <label for="attendance-month">Select Month:</label>
                    <input type="month" id="attendance-month" class="form-control" value="{{ Carbon\Carbon::now()->format('Y-m') }}">
                </div>
                
                <div class="attendance-calendar">
                    <div class="d-flex justify-content-around attendance-legend mb-3">
                        <div><span class="badge badge-success">Present</span></div>
                        <div><span class="badge badge-warning">Half Day</span></div>
                        <div><span class="badge badge-danger">Absent</span></div>
                        <div><span class="badge badge-secondary">Not Marked</span></div>
                    </div>
                    
                    <div id="attendance-days-container" class="mb-3">
                        <!-- Days will be loaded here -->
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Click on a date to toggle attendance status: Present → Half Day → Absent → Not Marked
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Attendance History Modal -->
<div class="modal fade" id="attendanceHistoryModal" tabindex="-1" role="dialog" aria-labelledby="attendanceHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceHistoryModalLabel">Attendance History for <span id="history-staff-name"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="history-person-id">
                
                <div class="mb-3">
                    <label for="history-month">Select Month:</label>
                    <input type="month" id="history-month" class="form-control" value="{{ Carbon\Carbon::now()->format('Y-m') }}">
                </div>
                
                <div id="attendance-history-container">
                    <!-- History will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add CSRF token meta tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

@push('styles')
<style>
    .btn-sm { padding: 0.25rem 0.5rem; }
    .badge { font-size: 90%; }
    .form-control-sm { height: calc(1.5em + 0.5rem + 2px); }
    .alert { margin-bottom: 1rem; }
    
    /* Category header styling */
    .category-header {
        background-color: #f8f9fa;
    }
    
    .category-header td {
        font-weight: bold;
        color: #495057;
        background-color: #e9ecef;
        border-top: 2px solid #dee2e6;
        padding: 0.5rem 0.75rem;
    }
    
    /* Improved Attendance Calendar Styles */
    .attendance-calendar {
        max-width: 100%;
        margin: 0 auto;
    }
    
    .list-group-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .list-group-item:hover:not(.disabled) {
        background-color: #f8f9fa;
    }
    
    .list-group-item.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .badge-pill {
        padding: 0.5rem 0.8rem;
        font-size: 90%;
    }
    
    .attendance-legend {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }
    
    /* Person search results */
    .person-result {
        padding: 10px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .person-result:hover {
        background-color: #f8f9fa;
    }
    
    .person-result.selected {
        background-color: #e3f2fd;
        border-color: #90caf9;
    }
    
    /* Fix for mobile view */
    @media (max-width: 576px) {
        .badge-pill {
            padding: 0.4rem 0.6rem;
            font-size: 80%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// Function to show alert messages
function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alert-container');
    const alert = `
        <div class="alert alert-${type} alert-dismissible fade show">
            ${message}
            <button type="button" class="close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    `;
    alertContainer.innerHTML = alert;
    setTimeout(() => {
        const alertElement = alertContainer.firstChild;
        if (alertElement) alertElement.remove();
    }, 3000);
}

// Function to mark attendance
async function markAttendance(personId, status) {
    try {
        const dateInput = document.getElementById(`date-${personId}`);
        const remarksInput = document.getElementById(`remarks-${personId}`);
        const statusCell = document.getElementById(`status-cell-${personId}`);
        const remarksCell = document.getElementById(`remarks-cell-${personId}`);

        // Show loading state
        const clickedButton = event.target;
        const originalText = clickedButton.innerText;
        clickedButton.innerText = 'Loading...';
        clickedButton.disabled = true;

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const response = await fetch('{{ route("attendance.manual.mark") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                person_id: personId,
                status: status,
                remarks: remarksInput.value,
                attendance_date: dateInput.value
            })
        });

        // Reset button state
        clickedButton.innerText = originalText;
        clickedButton.disabled = false;

        const data = await response.json();

        if (response.status === 403) {
            showAlert(data.message, 'danger');
            return;
        }

        if (data.success) {
            statusCell.innerHTML = data.status_badge;
            remarksCell.textContent = remarksInput.value || '-';
            showAlert(data.message, 'success');
            remarksInput.value = '';
        } else {
            showAlert(data.message, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('An error occurred while marking attendance', 'danger');
        
        // Make sure button is reset if there's an error
        if (event && event.target) {
            event.target.innerText = event.target.dataset.originalText || 'Present';
            event.target.disabled = false;
        }
    }
}

// Open the attendance calendar modal
function openAttendanceCalendar(personId, personName) {
    document.getElementById('modal-person-id').value = personId;
    document.getElementById('modal-staff-name').textContent = personName;
    
    // Load the calendar
    loadAttendanceCalendar();
    
    // Show the modal
    $('#attendanceCalendarModal').modal('show');
}

// Filter staff by category
function filterStaffByCategory(category) {
    if (category === 'all') {
        // Show all categories and their headers
        document.querySelectorAll('tr.category-header').forEach(header => {
            header.style.display = '';
        });
        document.querySelectorAll('tr[data-category]').forEach(row => {
            row.style.display = '';
        });
    } else {
        // Hide all category headers first
        document.querySelectorAll('tr.category-header').forEach(header => {
            header.style.display = 'none';
        });
        
        // Show only the selected category's header
        const headers = document.querySelectorAll('tr.category-header');
        for (let i = 0; i < headers.length; i++) {
            const headerText = headers[i].textContent.trim().toLowerCase();
            if (headerText.includes(category.replace('_', ' '))) {
                headers[i].style.display = '';
            }
        }
        
        // Show/hide staff rows based on category
        document.querySelectorAll('tr[data-category]').forEach(row => {
            if (row.getAttribute('data-category') === category) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
}

// Open attendance history modal
function openAttendanceHistory(personId, personName) {
    document.getElementById('history-person-id').value = personId;
    document.getElementById('history-staff-name').textContent = personName;
    
    // Load the attendance history
    loadAttendanceHistory();
    
    // Show the modal
    $('#attendanceHistoryModal').modal('show');
}

// Load attendance calendar for the selected month
async function loadAttendanceCalendar() {
    const personId = document.getElementById('modal-person-id').value;
    const month = document.getElementById('attendance-month').value;
    const daysContainer = document.getElementById('attendance-days-container');
    
    daysContainer.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    `;
    
    try {
        // Add a timestamp to prevent caching issues
        const timestamp = new Date().getTime();
        const response = await fetch(`{{ url('manual-attendance/staff') }}/${personId}/history?month=${month}&_=${timestamp}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            cache: 'no-store' // Prevent caching
        });
        
        if (!response.ok) {
            throw new Error(`Server responded with status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (response.status === 403) {
            daysContainer.innerHTML = `
                <div class="alert alert-danger">
                    ${data.message}
                </div>
            `;
            return;
        }
        
        if (data.success) {
            console.log('Received attendance data:', data); // Debug log
            renderAttendanceDays(data.dates);
        } else {
            daysContainer.innerHTML = `
                <div class="alert alert-danger">
                    Failed to load attendance data
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading attendance data:', error);
        daysContainer.innerHTML = `
            <div class="alert alert-danger">
                An error occurred while loading attendance data: ${error.message}
            </div>
        `;
    }
}

// Render the attendance days in a list format
function renderAttendanceDays(datesData) {
    const daysContainer = document.getElementById('attendance-days-container');
    const monthData = document.getElementById('attendance-month').value.split('-');
    const year = parseInt(monthData[0]);
    const month = parseInt(monthData[1]) - 1; // JS months are 0-indexed
    
    const today = new Date();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    
    // Get the month name
    const monthName = firstDay.toLocaleString('default', { month: 'long' });
    
    let html = `
        <div class="text-center mb-3">
            <h4>${monthName} ${year}</h4>
        </div>
        <div class="list-group">
    `;
    
    // Create a day for each date in the month
    for (let day = 1; day <= lastDay.getDate(); day++) {
        const date = new Date(year, month, day);
        // Format date as YYYY-MM-DD consistently
        const dateKey = formatDateYMD(date);
        const dateData = datesData[dateKey] || { status: 'not_marked', remarks: '' };
        
        console.log(`Day ${day} data:`, { dateKey, dateData }); // Debug log
        
        const isFuture = date > today;
        const dayName = date.toLocaleString('default', { weekday: 'short' });
        
        // Determine the status class and text
        let statusClass = '';
        let statusText = '';
        
        switch (dateData.status) {
            case 'present':
                statusClass = 'success';
                statusText = 'Present';
                break;
            case 'half':
                statusClass = 'warning';
                statusText = 'Half Day';
                break;
            case 'absent':
                statusClass = 'danger';
                statusText = 'Absent';
                break;
            default:
                statusClass = 'secondary';
                statusText = 'Not Marked';
        }
        
        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center ${isFuture ? 'disabled' : ''}" 
                 data-date="${dateKey}" 
                 ${!isFuture ? `onclick="toggleAttendance('${dateKey}')"` : ''}>
                <div>
                    <strong>${day}</strong> ${dayName}
                    ${dateData.remarks ? `<small class="d-block text-muted">${dateData.remarks}</small>` : ''}
                </div>
                <span class="badge badge-${statusClass} badge-pill">${statusText}</span>
            </div>
        `;
    }
    
    html += `</div>`;
    
    daysContainer.innerHTML = html + `
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> Click on a date to toggle attendance status: Present → Half Day → Absent → Not Marked
        </div>
    `;
}

// Toggle attendance status when clicking on a calendar day
async function toggleAttendance(date) {
    const personId = document.getElementById('modal-person-id').value;
    
    console.log(`Toggling attendance for person ${personId} on date ${date}`); // Debug log
    
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const response = await fetch('{{ route("attendance.manual.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                person_id: personId,
                attendance_date: date
            })
        });
        
        if (!response.ok) {
            throw new Error(`Server responded with status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Toggle response:', data); // Debug log
        
        if (response.status === 403) {
            showAlert(data.message, 'danger');
            return;
        }
        
        if (data.success) {
            // Reload the calendar to show the updated status
            await loadAttendanceCalendar();
            
            // Show a small notification
            showAlert(`Attendance ${data.status === 'not_marked' ? 'cleared' : 'marked as ' + data.status}`, 'success');
        }
    } catch (error) {
        console.error('Error toggling attendance:', error);
        showAlert('An error occurred while updating attendance: ' + error.message, 'danger');
    }
}

// Format date as YYYY-MM-DD
function formatDateYMD(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // months are 0-indexed
    const day = String(date.getDate()).padStart(2, '0');
    
    return `${year}-${month}-${day}`;
}

// Load attendance history for the selected month
async function loadAttendanceHistory() {
    const personId = document.getElementById('history-person-id').value;
    const month = document.getElementById('history-month').value;
    const historyContainer = document.getElementById('attendance-history-container');
    
    historyContainer.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    `;
    
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const response = await fetch(`{{ url('manual-attendance/staff') }}/${personId}/history?month=${month}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            }
        });
        
        const data = await response.json();
        
        if (response.status === 403) {
            historyContainer.innerHTML = `
                <div class="alert alert-danger">
                    ${data.message}
                </div>
            `;
            return;
        }
        
        if (data.success) {
            renderAttendanceHistory(data.dates);
        } else {
            historyContainer.innerHTML = `
                <div class="alert alert-danger">
                    Failed to load attendance data
                </div>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        historyContainer.innerHTML = `
            <div class="alert alert-danger">
                An error occurred while loading attendance data: ${error.message}
            </div>
        `;
    }
}

// Render attendance history table
function renderAttendanceHistory(datesData) {
    const historyContainer = document.getElementById('attendance-history-container');
    const month = document.getElementById('history-month').value;
    
    let tableHTML = `
        <div class="table-responsive">
            <table class="table table-bordered table-striped attendance-history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    // Sort dates by date (newest first)
    const sortedDates = Object.values(datesData).sort((a, b) => {
        return new Date(b.date) - new Date(a.date);
    });
    
    // Create rows for each date
    sortedDates.forEach(dateData => {
        // Determine the status class and text
        let statusClass = '';
        let statusText = '';
        
        switch (dateData.status) {
            case 'present':
                statusClass = 'success';
                statusText = 'Present';
                break;
            case 'half':
                statusClass = 'warning';
                statusText = 'Half Day';
                break;
            case 'absent':
                statusClass = 'danger';
                statusText = 'Absent';
                break;
            default:
                statusClass = 'secondary';
                statusText = 'Not Marked';
        }
        
        tableHTML += `
            <tr>
                <td>${dateData.formatted_date}</td>
                <td>${dateData.day_name}</td>
                <td><span class="badge badge-${statusClass}">${statusText}</span></td>
                <td>${dateData.remarks || '-'}</td>
            </tr>
        `;
    });
    
    tableHTML += `
                </tbody>
            </table>
        </div>
    `;
    
    historyContainer.innerHTML = tableHTML;
}

// Add event listeners when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded, initializing attendance functions');
    
    // Calendar month change
    const attendanceMonth = document.getElementById('attendance-month');
    if (attendanceMonth) {
        attendanceMonth.addEventListener('change', loadAttendanceCalendar);
    }
    
    // History month change
    const historyMonth = document.getElementById('history-month');
    if (historyMonth) {
        historyMonth.addEventListener('change', loadAttendanceHistory);
    }
    
    // Category filter
    const categoryFilter = document.getElementById('category-filter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            filterStaffByCategory(this.value);
        });
    }
    
    // Save original button text for all attendance buttons
    document.querySelectorAll('button[onclick^="markAttendance"]').forEach(button => {
        button.dataset.originalText = button.innerText;
    });
    
    console.log('Attendance functionality initialized successfully');
});
</script>
@endpush
@endsection