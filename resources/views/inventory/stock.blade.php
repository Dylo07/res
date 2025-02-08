@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content">
        @include('inventory.inc.sidebar')
        
        <div class="col-md-8">
            <i class="fa-solid fa-store"></i> Stock
            <hr>
            
            @if(Session()->has('status'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert"></button>
                {{Session()->get('status')}}
            </div>
            @endif

            @if(Session()->has('warning'))
            <div class="alert alert-warning">
                <button type="button" class="close" data-dismiss="alert"></button>
                {{Session()->get('warning')}}
            </div>
            @endif
            
            @foreach($data['categories'] as $category)
                <h4 class="mt-4">{{ $category->name }}</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Current Stock</th>
                            <th scope="col">Add Stock</th>
                            <th scope="col">Remove Stock</th>
                            <th scope="col">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['menus'][$category->id] ?? [] as $menu)
                        <tr>
                            <td>{{$menu->name}}</td>
                            <td>{{$menu->stock}}</td>
                            <td>
                                <form action="/inventory/storestock/{{$menu->id}}" method="post">
                                    @csrf
                                    @method('POST')
                                    <div style="width:130px">
                                        <input type="number" name="stock" style="width:80px" min="1">
                                        <input type="submit" value="Add" class="btn btn-danger btn-sm">
                                    </div>
                                </form>
                            </td>
                            <td>
                                <form action="/inventory/removeStock/{{$menu->id}}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <div style="width:170px">
                                        <input type="number" name="stock" style="width:80px" min="1">
                                        <input type="submit" value="Remove" class="btn btn-danger btn-sm">
                                    </div>
                                </form>
                            </td>
                            <td>
                                <a href="/inventory/stock/{{$menu->id}}" class="btn btn-primary">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>
    </div>
</div>
@endsection