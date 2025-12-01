@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Persons/Shops</h1>
    <div class="mb-3">
        <a href="{{ route('persons.create') }}" class="btn btn-primary">Add New Person/Shop</a>
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
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($persons as $person)
                <tr>
                    <td>{{ $person->id }}</td>
                    <td>{{ $person->name }}</td>
                    <td>{{ $person->type }}</td>
                    <td>
                        <a href="{{ route('persons.edit', $person->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('persons.destroy', $person->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No persons/shops found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
