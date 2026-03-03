@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content">
        @include('management.inc.sidebar')
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/fontawesome.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
        
        <div class="col-md-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><i class="fas fa-history"></i> Menu Activity Log</h4>
                <a href="/management/menu" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Menu
                </a>
            </div>
            
            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('menu.activity-log') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="action" class="form-control form-control-sm">
                                    <option value="">All Actions</option>
                                    <option value="Create" {{ request('action') == 'Create' ? 'selected' : '' }}>Create</option>
                                    <option value="Update" {{ request('action') == 'Update' ? 'selected' : '' }}>Update</option>
                                    <option value="Delete" {{ request('action') == 'Delete' ? 'selected' : '' }}>Delete</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="user" class="form-control form-control-sm" placeholder="Search by user..." value="{{ request('user') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Activity Log Table -->
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th width="12%">Date/Time</th>
                            <th width="10%">User</th>
                            <th width="8%">Action</th>
                            <th width="15%">Menu</th>
                            <th width="40%">Details</th>
                            <th width="15%">Price Change</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td><small>{{ date('m/d/Y H:i', strtotime($log->created_at)) }}</small></td>
                            <td>{{ $log->user_name }}</td>
                            <td>
                                @if($log->action == 'Create')
                                    <span class="badge badge-success">{{ $log->action }}</span>
                                @elseif($log->action == 'Update')
                                    <span class="badge badge-warning">{{ $log->action }}</span>
                                @else
                                    <span class="badge badge-danger">{{ $log->action }}</span>
                                @endif
                            </td>
                            <td><strong>{{ $log->menu_name }}</strong></td>
                            <td><small>{{ $log->details }}</small></td>
                            <td>
                                @if($log->old_price && $log->new_price)
                                    @if($log->old_price != $log->new_price)
                                        <span class="text-danger">Rs {{ number_format($log->old_price, 2) }}</span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span class="text-success">Rs {{ number_format($log->new_price, 2) }}</span>
                                    @else
                                        <span class="text-muted">No change</span>
                                    @endif
                                @elseif($log->new_price)
                                    <span class="text-success">Rs {{ number_format($log->new_price, 2) }}</span>
                                @elseif($log->old_price)
                                    <span class="text-danger">Rs {{ number_format($log->old_price, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="fas fa-info-circle"></i> No activity logs found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-3">
                {{ $logs->appends(request()->query())->links() }}
            </div>
            
            <!-- Summary Stats -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Showing {{ $logs->count() }} of {{ $logs->total() }} logs
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

@endsection
