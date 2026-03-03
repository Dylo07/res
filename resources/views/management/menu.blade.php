@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content">
        @include('management.inc.sidebar')
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/fontawesome.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
        
        <div class="col-md-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><i class="fa-solid fa-bowl-rice"></i> Menu Management</h4>
                <div>
                    <a href="{{ route('menu.activity-log') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-history"></i> Activity Log
                    </a>
                    <a href="/management/menu/create" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Create Menu
                    </a>
                </div>
            </div>
            
            @if(Session()->has('status'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{Session()->get('status')}}
            </div>
            @endif
            
            <!-- Category Filter Badges -->
            <div class="mb-3">
                <button class="btn btn-sm btn-primary category-badge active" data-category="all" onclick="filterByCategory('all')">
                    All <span class="badge badge-light">{{$menus->count()}}</span>
                </button>
                @foreach($categories as $category)
                    @if($category->menus->count() > 0)
                    <button class="btn btn-sm btn-info category-badge" data-category="{{$category->id}}" onclick="filterByCategory('{{$category->id}}')">
                        {{$category->name}} <span class="badge badge-light">{{$category->menus->count()}}</span>
                    </button>
                    @endif
                @endforeach
            </div>
            
            <!-- Search Box -->
            <div class="mb-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by name...">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Results Count -->
            <div class="mb-2">
                <small class="text-muted">Showing <span id="resultCount">{{$menus->count()}}</span> of {{$menus->count()}} menus</small>
            </div>
            
            <!-- Menu Table -->
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">Img</th>
                            <th width="25%">Name</th>
                            <th width="15%">Price</th>
                            <th width="20%">Category</th>
                            <th width="15%">Description</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="menuTableBody">
                        @foreach($menus as $index => $menu)
                        <tr class="menu-row" data-name="{{strtolower($menu->name)}}" data-category="{{$menu->category_id}}">
                            <td>{{$index + 1}}</td>
                            <td>
                                <img src="{{asset('menu_images')}}/{{$menu->image}}" alt="{{$menu->name}}" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><strong>{{$menu->name}}</strong></td>
                            <td><span class="badge badge-success">Rs {{number_format($menu->price, 2)}}</span></td>
                            <td>{{$menu->category->name}}</td>
                            <td><small class="text-muted">{{$menu->description ?: 'No recipe'}}</small></td>
                            <td>
                                <a href="/management/menu/{{$menu->id}}/edit" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{$menu->id}}, '{{$menu->name}}')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{$menu->id}}" action="/management/menu/{{$menu->id}}" method="post" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- No Results Message -->
            <div id="noResults" class="alert alert-info" style="display: none;">
                <i class="fas fa-info-circle"></i> No menu items found matching your filters.
            </div>
        </div>
    </div>
</div>

<style>
    .category-badge {
        margin-right: 5px;
        margin-bottom: 5px;
        border-radius: 20px;
        transition: all 0.2s;
    }
    .category-badge.active {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: white !important;
    }
    .category-badge:hover {
        transform: scale(1.05);
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .menu-row {
        transition: opacity 0.2s;
    }
</style>

<script>
    let currentCategory = 'all';
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterMenuItems();
    });
    
    function filterByCategory(categoryId) {
        currentCategory = categoryId;
        
        // Update active badge
        document.querySelectorAll('.category-badge').forEach(badge => {
            badge.classList.remove('active');
        });
        document.querySelector(`[data-category="${categoryId}"]`).classList.add('active');
        
        filterMenuItems();
    }
    
    function filterMenuItems() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.menu-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const itemName = row.getAttribute('data-name');
            const itemCategory = row.getAttribute('data-category');
            
            const matchesSearch = itemName.includes(searchTerm);
            const matchesCategory = currentCategory === 'all' || itemCategory === currentCategory;
            
            if (matchesSearch && matchesCategory) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update count
        document.getElementById('resultCount').textContent = visibleCount;
        
        // Show/hide no results message
        document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
    }
    
    function clearSearch() {
        document.getElementById('searchInput').value = '';
        filterMenuItems();
    }
    
    function confirmDelete(id, name) {
        if (confirm('Are you sure you want to delete "' + name + '"?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>

@endsection