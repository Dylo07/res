@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back, {{ Auth::user()->name }}!</p>
    </div>

    <div class="row g-4">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <!-- Main Navigation -->
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title-icon"><i data-lucide="layout-grid"></i> Main Menu</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach([
                            ['route' => 'management', 'title' => 'Management', 'icon' => 'settings-2'],
                            ['route' => 'cashier', 'title' => 'Cashier', 'icon' => 'shopping-cart'],
                            ['route' => 'inventory', 'title' => 'Water & Soft Drink', 'icon' => 'beer'],
                            ['route' => 'report', 'title' => 'Report', 'icon' => 'file-bar-chart-2', 'admin' => true],
                            ['url' => '/calendar', 'title' => 'Booking Calendar', 'icon' => 'calendar-days'],
                            ['url' => '/stock', 'title' => 'Grocery Item Store', 'icon' => 'shopping-basket'],
                            ['url' => '/inv-inventory', 'title' => 'Physical Item Inventory', 'icon' => 'archive'],
                            ['url' => '/costs', 'title' => 'Daily Expense', 'icon' => 'wallet'],
                            ['url' => '/cashier/balance', 'title' => 'Cashier Balance', 'icon' => 'scale'],
                            ['url' => '/tasks', 'title' => 'Daily Tasks', 'icon' => 'check-square'],
                            ['url' => '/manual-attendance', 'title' => 'Manual Attendance', 'icon' => 'user-check'],
                            ['url' => '/damage-items', 'title' => 'Damage Items', 'icon' => 'shield-alert'],
                            ['url' => '/salary', 'title' => 'Monthly Salary', 'icon' => 'landmark'],
                            ['url' => '/service-charge', 'title' => 'Service Charge', 'icon' => 'receipt'],
                        ] as $item)
                            @if(!isset($item['admin']) || (isset($item['admin']) && Auth::user()->checkAdmin()))
                                <div class="col-lg-3 col-md-4 col-6">
                                    <a href="{{ isset($item['route']) ? route($item['route']) : $item['url'] }}" class="menu-card-link">
                                        <div class="menu-card-item">
                                            <div class="icon-wrapper">
                                                <i data-lucide="{{ $item['icon'] }}"></i>
                                            </div>
                                            <span>{{ $item['title'] }}</span>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            
            <!-- Pending Tasks -->
            <div class="card dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="card-title-icon"><i data-lucide="list-todo"></i> Pending Tasks</h5>
                    <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-outline-primary ms-auto">Add Task</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-card-flush mb-0">
                            <tbody>
                                @forelse($pendingTasks as $task)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $task->task }}</span>
                                            <small class="text-muted">{{ $task->taskCategory->name }} | {{ $task->person_incharge }}</small>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if($task->priority_order == 'High')
                                            <span class="badge bg-light-danger text-danger">High</span>
                                        @elseif($task->priority_order == 'Medium')
                                            <span class="badge bg-light-warning text-warning">Medium</span>
                                        @else
                                            <span class="badge bg-light-success text-success">Low</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">No pending tasks.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Salary Advances -->
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title-icon"><i data-lucide="hand-coins"></i> Salary Advances</h5>
                    <a href="{{ route('costs.create') }}" class="btn btn-sm btn-outline-primary ms-auto">Add Advance</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-card-flush mb-0">
                            <tbody>
                                @forelse($salaryAdvances->groupBy('person.name') as $employeeName => $advances)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $employeeName }}</span>
                                                <small class="text-muted">{{ $advances->count() }} advance(s)</small>
                                            </div>
                                        </td>
                                        <td class="text-end text-danger fw-bold">Rs. {{ number_format($advances->sum('amount'), 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">No salary advances found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="fw-bold">Total</td>
                                    <td class="text-end fw-bold">Rs. {{ number_format($totalAdvance, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();
</script>
@endpush

@push('styles')
<style>
    :root {
        --dash-card-bg: #fff;
        --dash-card-border: #eef2f7;
        --dash-text-primary: #344767;
        --dash-text-secondary: #67748e;
        --dash-icon-color: #344767;
        --dash-accent-color: #3B82F6;
    }

    body {
        background-color: #f8f9fa;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--dash-text-primary);
    }

    .page-subtitle {
        font-size: 1rem;
        color: var(--dash-text-secondary);
    }

    .dashboard-card {
        border: none;
        border-radius: 0.75rem;
        background-color: var(--dash-card-bg);
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        overflow: hidden;
    }

    .dashboard-card .card-header {
        display: flex;
        align-items: center;
        padding: 1rem 1.25rem;
        background-color: transparent;
        border-bottom: 1px solid var(--dash-card-border);
    }

    .card-title-icon {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        color: var(--dash-text-primary);
        margin-bottom: 0;
    }

    .card-title-icon i {
        width: 18px;
        height: 18px;
    }

    .menu-card-link {
        text-decoration: none;
    }

    .menu-card-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 1rem;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
        border: 1px solid var(--dash-card-border);
        color: var(--dash-text-secondary);
        transition: all 0.2s ease-in-out;
        height: 100%;
    }

    .menu-card-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        background-color: var(--dash-card-bg);
        color: var(--dash-accent-color);
    }

    .menu-card-item .icon-wrapper {
        margin-bottom: 0.75rem;
    }

    .menu-card-item .icon-wrapper i {
        width: 28px;
        height: 28px;
    }

    .menu-card-item span {
        font-size: 0.8rem;
        font-weight: 500;
    }

    .service-charge-summary {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .stat-item {
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
    }

    .stat-item.wide {
        grid-column: 1 / -1;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--dash-text-secondary);
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dash-text-primary);
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .icon-sm {
        width: 16px;
        height: 16px;
    }

    .table-card-flush tbody tr:first-child td {
        border-top: none;
    }

    .table-card-flush td {
        padding: 1rem 1.25rem;
    }

    .badge.bg-light-danger {
        background-color: rgba(234, 57, 67, 0.15) !important;
    }

    .badge.bg-light-warning {
        background-color: rgba(255, 193, 7, 0.15) !important;
    }

    .badge.bg-light-success {
        background-color: rgba(40, 167, 69, 0.15) !important;
    }
</style>
@endpush