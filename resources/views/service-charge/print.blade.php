@extends('layouts.print')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Service Charge Receipt</h1>
            <button onclick="window.print()" class="btn btn-light">Print Receipt</button>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Employee Details</h4>
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Name:</th>
                            <td>{{ $serviceCharge->person->name }}</td>
                        </tr>
                        <tr>
                            <th>Month/Year:</th>
                            <td>{{ Carbon\Carbon::create()->month($serviceCharge->month)->format('F') }} {{ $serviceCharge->year }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <h4>Service Charge Details</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Service Charge Pool:</th>
                            <td>Rs. {{ number_format($serviceCharge->total_sc, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Points Ratio:</th>
                            <td>{{ number_format($serviceCharge->points_ratio * 100, 2) }}%</td>
                        </tr>
                        <tr>
                            <th>Final Amount:</th>
                            <td>Rs. {{ number_format($serviceCharge->final_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Remarks:</th>
                            <td>{{ $serviceCharge->remarks }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-6">
                    <div class="border-top border-dark pt-2">
                        Employee Signature
                    </div>
                </div>
                <div class="col-6 text-end">
                    <div class="border-top border-dark pt-2">
                        Authorized Signature
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection