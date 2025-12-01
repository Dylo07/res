@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Stock Details</h2>

    <!-- Month and Year Selection Form -->
    <form action="{{ route('inv_inventory.monthly') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label for="month" class="form-label">Select Month</label>
                <select name="month" id="month" class="form-select">
                    @foreach(range(1, 12) as $m)
                        @php
                            $monthNum = str_pad($m, 2, '0', STR_PAD_LEFT);
                            $dateObj = DateTime::createFromFormat('!m', $monthNum);
                        @endphp
                        <option value="{{ $monthNum }}" {{ $month == $monthNum ? 'selected' : '' }}>
                            {{ $dateObj->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="year" class="form-label">Select Year</label>
                <select name="year" id="year" class="form-select">
                    @foreach(range($year - 5, $year + 5) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary mt-2">View Stock</button>
            </div>
        </div>
    </form>

    <h3>Stock Details for {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</h3>

    @php
        use Carbon\Carbon;
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
    @endphp

    @foreach($categories as $category)
        <h4>{{ $category->name }}</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    @for($i = 1; $i <= $daysInMonth; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($category->products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        @for($i = 1; $i <= $daysInMonth; $i++)
                            @php
                                $date = sprintf('%04d-%02d-%02d', $year, $month, $i);
                                $inventory = $product->inventories->firstWhere('stock_date', $date);

                                if ($inventory) {
                                    $displayStock = $inventory->stock_level;
                                } else {
                                    $previousInventory = $product->inventories->where('stock_date', '<', $date)->sortByDesc('stock_date')->first();
                                    $displayStock = $previousInventory ? $previousInventory->stock_level : '-';
                                }
                            @endphp
                            <td>{{ $displayStock }}</td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
@endsection
