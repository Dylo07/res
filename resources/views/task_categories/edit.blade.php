@extends('layouts.app')

@section('content')
<h1>Edit Task Category</h1>
<form action="{{ route('task-categories.update', $category->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="name">Category Name</label>
        <input type="text" class="form-control" name="name" value="{{ $category->name }}" required>
    </div>
    <button type="submit" class="btn btn-success">Update Category</button>
</form>
@endsection
