@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Group</h1>
    <form action="{{ route('groups.update', $group->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Group Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $group->name }}" placeholder="Enter group name" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success mt-3">Update</button>
        <a href="{{ route('groups.index') }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>
@endsection
