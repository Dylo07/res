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

            @if(Session()->has('error'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert"></button>
                {{Session()->get('error')}}
            </div>
            @endif
            
            <div class="row">
                <div class="col-md-12 mb-4">
                    <!-- Include the Daily Sales Component -->
                    @include('inventory.daily-sales')
                </div>
            </div>
            
            <!-- Link to merged products management -->
            @if(isset($data['categories']) && $data['categories']->contains('id', 29))
            <div class="mb-3">
                <a href="{{ route('merged-products.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-object-group"></i> Manage Merged Products
                </a>
            </div>
            @endif
            
            @foreach($data['categories'] as $category)
            <div class="card mb-4">
                <div class="card-header">
                    <h4>{{ $category->name }}</h4>
                </div>
                <div class="card-body">
                    @if($category->id == 29)
                        <!-- Special handling for category 29 (liquor) -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Current Stock</th>
                                    <th scope="col">Total ML</th>
                                    <th scope="col">Add Stock</th>
                                    <th scope="col">Remove Stock</th>
                                    <th scope="col">View</th>
                                </tr>  
                            </thead>
                            <tbody>
                                @php
                                    // Group items by base name (without ml size)
                                    $groupedItems = [];
                                    foreach($data['menus'][$category->id] ?? [] as $menu) {
                                        $baseName = preg_replace('/\(\d+\s*ml\)/i', '', $menu->name);
                                        $baseName = trim($baseName);
                                        
                                        if (!isset($groupedItems[$baseName])) {
                                            $groupedItems[$baseName] = [
                                                'items' => [],
                                                'total_ml' => 0,
                                                'parent' => null
                                            ];
                                        }
                                        
                                        // Extract ml value
                                        preg_match('/\((\d+)\s*ml\)/i', $menu->name, $matches);
                                        $menuMl = isset($matches[1]) ? (int)$matches[1] : 0;
                                        
                                        // Add to group
                                        $groupedItems[$baseName]['items'][] = [
                                            'menu' => $menu,
                                            'ml_value' => $menuMl,
                                            'total_ml' => $menuMl * $menu->stock
                                        ];
                                        
                                        $groupedItems[$baseName]['total_ml'] += $menuMl * $menu->stock;
                                        
                                        // If this is 750ml, mark as parent
                                        if ($menuMl == 750) {
                                            $groupedItems[$baseName]['parent'] = $menu;
                                        }
                                    }
                                @endphp
                                
                                @foreach($groupedItems as $baseName => $group)
                                    @php
                                        // Determine if we have a parent item
                                        $hasParent = $group['parent'] !== null;
                                        
                                        // Calculate equivalent 750ml bottles
                                        $totalMl = $group['total_ml'];
                                        $equivalent750ml = floor($totalMl / 750);
                                        $remainingMl = $totalMl % 750;
                                    @endphp
                                    
                                    <!-- Display base name as header -->
                                    <tr class="table-secondary">
                                        <td colspan="6">
                                            <strong>{{ $baseName }}</strong>
                                            <span class="badge badge-info">Total: {{ $totalMl }} ml</span>
                                            <span class="badge badge-primary">{{ $equivalent750ml }} full 750ml bottles + {{ $remainingMl }} ml</span>
                                            @if(count($group['items']) > 1)
                                                <a href="{{ route('merged-products.index') }}" class="btn btn-sm btn-outline-primary float-right">Manage</a>
                                            @endif
                                        </td>
                                    </tr>
                                    
                                    <!-- Display all items in this group -->
                                    @foreach($group['items'] as $item)
                                        <tr>
                                            <td>{{ $item['menu']->name }}</td>
                                            <td>{{ $item['menu']->stock }}</td>
                                            <td>
                                                @if($item['ml_value'] == 750)
                                                    <strong>{{ $totalMl }} ml</strong> <span class="badge badge-success">Source</span>
                                                @else
                                                    {{ $item['total_ml'] }} ml
                                                @endif
                                            </td>
                                            <td>
                                                <form action="/inventory/storestock/{{$item['menu']->id}}" method="post">
                                                    @csrf
                                                    @method('POST')
                                                    <div style="width:130px">
                                                        <input type="number" name="stock" style="width:80px" min="1">            
                                                        <input type="submit" value="Add" class="btn btn-danger btn-sm">
                                                    </div>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="/inventory/removeStock/{{$item['menu']->id}}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div style="width:170px">
                                                        <input type="number" name="stock" style="width:80px" min="1">            
                                                        <input type="submit" value="Remove" class="btn btn-danger btn-sm">
                                                    </div>
                                                </form>
                                            </td>
                                            <td>
                                                <a href="/inventory/stock/{{$item['menu']->id}}" class="btn btn-primary">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <!-- Standard handling for other categories -->
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
                    @endif
                </div>
            </div>
            @endforeach
            
        </div>
    </div>
</div>
@endsection