@extends('layouts.app')

@section('content')
<h1>Create Task</h1>
<form action="{{ route('tasks.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="user">User</label>
        <input type="text" class="form-control" name="user" required>
    </div>
    <div class="form-group">
        <label for="date_added">Date Added</label>
        <input type="date" class="form-control" name="date_added" required>
    </div>
    <div class="form-group">
        <label for="task">Task</label>
        <input type="text" class="form-control" name="task" required>
    </div>
    <div class="form-group">
        <label for="task_category_id">Task Category</label>
        <select class="form-control" name="task_category_id" required>
            @foreach($taskCategories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="person_incharge">Person Incharge</label>
        <input type="text" class="form-control" name="person_incharge" required>
    </div>
    <div class="form-group">
        <label for="priority_order">Priority Order</label>
        <select class="form-control" name="priority_order" required>
            <option value="High">High</option>
            <option value="Medium">Medium</option>
            <option value="Low">Low</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Create Task</button>
</form>
@endsection
