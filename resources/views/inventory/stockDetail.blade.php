@extends('layouts.app')

@section('content')
<style type="text/css">
    i {
        font-size: 22px !important;
        padding: 10px;
    }
    .detail-card {
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 25px;
    }
    .detail-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px 8px 0 0;
        border-bottom: 2px solid #e9ecef;
    }
    .stock-value {
        font-weight: bold;
        font-size: 1.1em;
    }
    .history-table {
        margin-top: 10px;
    }
    .history-table th {
        background-color: #343a40;
        color: white;
    }
    .positive-stock {
        color: #28a745;
    }
    .negative-stock {
        color: #dc3545;
    }
    .neutral-stock {
        color: #6c757d;
    }
    .section-title {
        background-color: #343a40;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        margin: 20px 0;
        font-size: 1.2rem;
    }
    .bill-tag {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        background-color: #17a2b8;
        color: white;
        font-size: 0.8em;
        margin-left: 5px;
    }
</style>

<div class="container">
    <div class="row justify-content">
        @include('inventory.inc.sidebar')
        
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-3">
                <i class="fa-solid fa-store"></i> 
                <h4 class="mb-0">Stock Detail</h4>
            </div>
            <hr>
            
            <div class="detail-card">
                <div class="detail-header">
                    <h5 class="mb-0">Product Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th scope="row" width="30%">ID</th>
                                <td>{{$menu->id}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Name</th>
                                <td>{{$menu->name}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Category</th>
                                <td>{{$menu->category->name}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Current Stock</th>
                                <td class="stock-value">{{$menu->stock}}</td>
                            </tr>
                            @if($menu->category_id == 29)
                                @php
                                    preg_match('/\((\d+)\s*ml\)/i', $menu->name, $matches);
                                    $menuMl = isset($matches[1]) ? $matches[1] : 'N/A';
                                @endphp
                                <tr>
                                    <th scope="row">ML Per Unit</th>
                                    <td>{{$menuMl}} ml</td>
                                </tr>
                                <tr>
                                    <th scope="row">Total ML</th>
                                    <td>{{$menuMl * $menu->stock}} ml</td>
                                </tr>
                                @if(isset($menu->is_merge_parent) && $menu->is_merge_parent)
                                    <tr>
                                        <th scope="row">Merged Group Total ML</th>
                                        <td>{{$menu->total_ml}} ml</td>
                                    </tr>
                                @elseif(isset($menu->merge_parent))
                                    <tr>
                                        <th scope="row">Part of Merged Group</th>
                                        <td>Parent: {{$menu->merge_parent->name}}</td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            @if(isset($menu->is_merge_parent) && $menu->is_merge_parent)
                <div class="section-title">
                    Merged Products in Group
                </div>
                <div class="detail-card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Current Stock</th>
                                    <th scope="col">ML Per Unit</th>
                                    <th scope="col">Total ML</th>
                                </tr>  
                            </thead>
                            <tbody> 
                                @foreach($menu->merged_children as $child)
                                    @php
                                        preg_match('/\((\d+)\s*ml\)/i', $child->name, $matches);
                                        $childMl = isset($matches[1]) ? $matches[1] : 'N/A';
                                    @endphp
                                    <tr> 
                                        <td>{{$child->name}}</td>
                                        <td>{{$child->stock}}</td>
                                        <td>{{$childMl}} ml</td>
                                        <td>{{$childMl * $child->stock}} ml</td>
                                    </tr>
                                @endforeach
                                <tr class="table-info">
                                    <td colspan="3"><strong>Total ML</strong></td>
                                    <td><strong>{{$menu->total_ml}} ml</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="section-title">
                <i class="fa-solid fa-history"></i> Stock History
            </div>
            <div class="detail-card">
                <div class="card-body">
                    <table class="table table-bordered history-table">
                        <thead>
                            <tr>
                                <th scope="col">User</th>
                                <th scope="col">Product</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Bill Number</th>
                                <th scope="col">Date/Time</th>
                            </tr>  
                        </thead>
                        <tbody> 
                            @if(isset($menu->all_in_stock))
                                @foreach($menu->all_in_stock as $stock)
                                    <tr @if($stock->menu_id != $menu->id) class="table-secondary" @endif> 
                                        <td> {{$stock->user->name}}</td>
                                        <td> {{$stock->menu->name}} </td>
                                        <td class="{{ $stock->stock > 0 ? 'positive-stock' : ($stock->stock < 0 ? 'negative-stock' : 'neutral-stock') }}">
                                            {{ $stock->stock }}
                                        </td>
                                        <td>
                                            @if(isset($stock->sale_id) && $stock->sale_id)
                                                <span class="bill-tag">BILL #{{$stock->sale_id}}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{$stock->created_at->format('d-M-Y')}}
                                            </span>
                                            <span class="ml-1">
                                                {{$stock->created_at->format('h:i:s A')}}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach($menu->inStock as $stock)
                                    <tr> 
                                        <td> {{$stock->user->name}}</td>
                                        <td> {{$menu->name}} </td>
                                        <td class="{{ $stock->stock > 0 ? 'positive-stock' : ($stock->stock < 0 ? 'negative-stock' : 'neutral-stock') }}">
                                            {{ $stock->stock }}
                                        </td>
                                        <td>
                                            @if(isset($stock->sale_id) && $stock->sale_id)
                                                <span class="bill-tag">BILL #{{$stock->sale_id}}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{$stock->created_at->format('d-M-Y')}}
                                            </span>
                                            <span class="ml-1">
                                                {{$stock->created_at->format('h:i:s A')}}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection