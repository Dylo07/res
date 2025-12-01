@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Salary Payslip</h3>
            <button onclick="window.print()" class="btn btn-light">Print Payslip</button>
        </div>
        <div class="card-body">
            <!-- Employee Details Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="border-bottom pb-2">Employee Details</h4>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Name</th>
                                    <td>{{ $salary->person->name }}</td>
                                </tr>
                                <tr>
                                    <th>Month/Year</th>
                                    <td>{{ Carbon\Carbon::create()->month($salary->month)->format('F') }}, {{ $salary->year }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salary Details Section -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="bg-light" width="200">Basic Salary</th>
                            <td>Rs. {{ number_format($salary->basic_salary, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Present Days</th>
                            <td>{{ number_format($salary->present_days, 1) }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Absent Days</th>
                            <td>{{ number_format($salary->days_off, 1) }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Salary Advance</th>
                            <td class="text-danger">- Rs. {{ number_format($salary->salary_advance, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Remarks</th>
                            <td>{{ $salary->remarks }}</td>
                        </tr>
                        <tr class="table-dark">
                            <th>Final Salary</th>
                            <td class="fw-bold">Rs. {{ number_format($salary->final_salary, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Signature Section -->
            <div class="mt-5 pt-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="border-top border-dark"></div>
                        <p class="text-center mt-2">Employee Signature</p>
                    </div>
                    <div class="col-md-4 offset-md-4">
                        <div class="border-top border-dark"></div>
                        <p class="text-center mt-2">Authorized Signature</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn { 
            display: none; 
        }
        .card { 
            border: none !important; 
        }
        .card-header { 
            background-color: #333 !important; 
            color: white !important;
            -webkit-print-color-adjust: exact; 
        }
        .table-dark {
            background-color: #333 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
        }
        .bg-light {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
        }
        .border-top {
            border-top: 1px solid #000 !important;
        }
        .text-danger {
            color: #dc3545 !important;
            -webkit-print-color-adjust: exact;
        }
    }

    .table-borderless th {
        font-weight: 600;
        color: #666;
    }
    
    .table-bordered th {
        background-color: #f8f9fa;
    }
</style>
@endsection