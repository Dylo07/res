@extends('layouts.app')

@section('content')
<h1>Task Categories</h1>
<div class="mb-3">
    <a href="{{ route('task-categories.create') }}" class="btn btn-primary">Create Task Category</a>
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">View Tasks</a>
</div>

<!-- Display Success Message -->
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- Display Error Messages -->
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Task Categories Table -->
<table class="table mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($categories as $category)
        <tr>
            <td>{{ $category->id }}</td>
            <td>{{ $category->name }}</td>
            <td>{{ $category->created_at->format('Y-m-d') }}</td>
            <td>
                <a href="{{ route('task-categories.edit', $category->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('task-categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4">No task categories found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
