@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Stock Details for {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</h2>

    @foreach($groups as $group)
        <h4>{{ $group->name }}</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item</th>
                    @for($i = 1; $i <= 31; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($group->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        @for($i = 1; $i <= 31; $i++)
                            @php
                                $date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                $inventory = $item->inventory->firstWhere('stock_date', $date);
                            @endphp
                            <td>{{ $inventory->stock_level ?? 0 }}</td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
@endsection