@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Cost</h1>
    <form action="{{ route('costs.update', $cost->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="group_id">Group</label>
            <select name="group_id" id="group_id" class="form-control" required>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}" {{ $cost->group_id == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="person_or_shop">Person/Shop</label>
            <input type="text" name="person_or_shop" id="person_or_shop" class="form-control" value="{{ $cost->person_or_shop }}" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" value="{{ $cost->amount }}" required>
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
        <button type="submit" class="btn btn-success mt-3">Update</button>
        <a href="{{ route('costs.index') }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>
@endsection
