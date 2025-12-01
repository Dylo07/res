@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Groups</h1>
    <div class="mb-3">
        <a href="{{ route('groups.create') }}" class="btn btn-primary">Add New Group</a>
        <a href="{{ route('costs.index') }}" class="btn btn-secondary">Go to Costs</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groups as $group)
                <tr>
                    <td>{{ $group->id }}</td>
                    <td>{{ $group->name }}</td>
                    <td>
                        <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('groups.destroy', $group->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No groups found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
