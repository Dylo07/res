@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Cost</h1>
    <form action="{{ route('costs.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="group_id">Group</label>
            <select name="group_id" id="group_id" class="form-control" required>
                <option value="">Select a group</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="person_id">Person/Shop</label>
            <select name="person_id" id="person_id" class="form-control" required>
                <option value="">Select a person/shop</option>
                @foreach ($persons as $person)
                    <option value="{{ $person->id }}">{{ $person->name }}</option>
                @endforeach
            </select>
            <a href="{{ route('persons.create') }}" class="btn btn-link mt-2">Add New Person/Shop</a>
        </div>


        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter amount" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter description"></textarea>
        </div>

        
        <div class="form-group">
    <label for="cost_date">Date</label>
    <input type="date" 
           name="cost_date" 
           id="cost_date" 
           class="form-control" 
           value="{{ date('Y-m-d') }}" 
           readonly>
</div>
        <button type="submit" class="btn btn-primary mt-3">Save</button>
        <a href="{{ route('costs.index') }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>
@endsection
