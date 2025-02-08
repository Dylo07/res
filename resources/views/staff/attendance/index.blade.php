@extends('layouts.app')

<style>
    .table td {
        white-space: nowrap;
        font-size: 12px;
        padding: 6px !important;
        text-align: center;
        border: 1px solid #e0e0e0;
    }
    .table th {
        white-space: nowrap;
        font-size: 12px;
        padding: 8px !important;
        text-align: center;
        background-color: #f3f4f6;
        border: 1px solid #e0e0e0;
        font-weight: bold;
    }
    .staff-info {
        text-align: left;
        font-weight: bold;
        vertical-align: middle !important;
        background-color: #f8f9fa;
        border-right: 2px solid #dee2e6 !important;
    }
    .staff-row {
        border-top: 2px solid #dee2e6;
    }
    .staff-separator {
        background-color: #e9ecef;
        height: 10px;
    }
    .time-cell {
        font-family: monospace;
        color: #333;
    }
    .table-responsive {
        overflow-x: auto;
        margin-top: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: bold;
        padding: 12px 20px;
    }
    .morning-time {
        color: #2563eb;
    }
    .evening-time {
        color: #dc2626;
    }
    .absent {
        color: #6c757d; /* Grey for absent */
    }

    
</style>

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Attendance Manager -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                <span>Attendance Manager</span>
                    <div class="d-flex align-items-center">
                        <a href="/manual-attendance" class="btn btn-primary me-3">Manual Attendance</a>
                        
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
