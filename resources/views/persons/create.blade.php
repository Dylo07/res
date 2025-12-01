@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Person/Shop</h1>
    <form action="{{ route('persons.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Person/Shop Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter name" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary mt-3">Save</button>
        <a href="{{ route('persons.index') }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>
@endsection
