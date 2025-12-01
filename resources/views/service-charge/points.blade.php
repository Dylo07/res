@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manage Service Charge Points</h5>
                    <a href="{{ route('service-charge.index') }}" class="btn btn-light btn-sm">
                        Back to Service Charge
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('service-charge.points.update-bulk') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staff as $employee)
                                    <tr>
                                        <td>{{ $employee->name }}</td>
                                        <td width="200">
                                            <input type="number" 
                                                   name="points[{{ $employee->id }}]" 
                                                   class="form-control" 
                                                   value="{{ $points[$employee->id]->points ?? 0 }}"
                                                   min="0">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                Save Points
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection