@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Manage Staff Categories</h3>
                <div>
                    <a href="{{ route('attendance.manual.index') }}" class="btn btn-secondary">Back to Attendance</a>
                </div>
            </div>
        </div>

        <div class="card-body">
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

            <form action="{{ route('attendance.manual.bulk-update-categories') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Person ID</th>
                                <th>Staff Name</th>
                                <th>Current Category</th>
                                <th>New Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $member)
                                <tr>
                                    <td>{{ $member->id }}</td>
                                    <td>{{ $member->name }}</td>
                                    <td>
                                        @if($member->staffCategory)
                                            <span class="badge badge-info">
                                                {{ ucfirst(str_replace('_', ' ', $member->staffCategory->category)) }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <select name="categories[{{ $member->id }}]" class="form-control">
                                            <option value="">-- Select Category --</option>
                                            @foreach($categories as $value => $name)
                                                <option value="{{ $value }}" {{ $member->staffCategory && $member->staffCategory->category == $value ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update Categories</button>
                </div>
            </form>
            
            <!-- Debug Information (Remove in production) -->
            <div class="mt-4">
                <div class="card bg-light">
                    <div class="card-header">Debug Info</div>
                    <div class="card-body">
                        <h5>Staff Count: {{ $staff->count() }}</h5>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Person ID</th>
                                    <th>Has staffCategory Relation?</th>
                                    <th>Category Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staff as $member)
                                    <tr>
                                        <td>{{ $member->id }}</td>
                                        <td>{{ $member->staffCategory ? 'Yes' : 'No' }}</td>
                                        <td>{{ $member->staffCategory ? $member->staffCategory->category : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection