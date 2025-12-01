{{-- resources/views/salary/basic.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Basic Salary Assignment</h3>
            <a href="{{ route('salary.index') }}" class="btn btn-outline-light">Back to Salary Processing</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Employee</th>
                            <th>Current Basic Salary</th>
                            <th>New Basic Salary</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td class="text-end">Rs. {{ number_format($employee->basic_salary ?? 0, 2) }}</td>
                            <td>
                                <input type="number" 
                                       id="basic-salary-{{ $employee->id }}"
                                       class="form-control text-end" 
                                       placeholder="Enter new basic salary">
                            </td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm save-basic-salary"
                                        data-person="{{ $employee->id }}">
                                    Save
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.save-basic-salary').click(function() {
        const personId = $(this).data('person');
        const basicSalary = $(`#basic-salary-${personId}`).val();

        if (!basicSalary) {
            alert('Please enter basic salary');
            return;
        }

        $.ajax({
            url: '{{ route("salary.updateBasic") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                person_id: personId,
                basic_salary: basicSalary
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Error saving basic salary');
            }
        });
    });
});
</script>
@endpush
@endsection