@extends('layouts.app')

@section('content')
<h1>Create Task Category</h1>
<form action="{{ route('task-categories.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="name">Category Name</label>
        <input type="text" class="form-control" name="name" required>
    </div>
    <button type="submit" class="btn btn-success">Create Category</button>
</form>
@endsection
