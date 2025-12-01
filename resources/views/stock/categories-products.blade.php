@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <!-- Card for Navigation -->
    <div class="card mb-4">
        <div class="card-body p-0">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a href="{{ route('stock.index') }}" 
                       class="nav-link {{ Request::routeIs('stock.index') ? 'active' : '' }}">
                        Stock Management
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('categories-products.index') }}" 
                       class="nav-link {{ Request::routeIs('categories-products.index') ? 'active' : '' }}">
                        Category & Product Management
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="row">
        <!-- Left Column - Category Management -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Category Management</h2>
                </div>
                <div class="card-body">
                    <!-- Add Category Form -->
                    <form action="{{ route('categories.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">New Category Name</label>
                            <input type="text" id="categoryName" name="name" required
                                class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            Add Category
                        </button>
                    </form>

                    <!-- Existing Categories List -->
                    <h3 class="mt-4 mb-3">Existing Categories</h3>
                    <div class="list-group">
                        @foreach($groups as $group)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $group->name }}</span>
                                <span class="badge bg-primary rounded-pill">
                                    {{ $group->items_count ?? 0 }} products
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Product Management -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Product Management</h2>
                </div>
                <div class="card-body">
                    <!-- Add Product Form -->
                    <form action="{{ route('items.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="productName" class="form-label">New Product Name</label>
                            <input type="text" id="productName" name="name" required
                                class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="group_id" class="form-label">Category</label>
                            <select id="group_id" name="group_id" required class="form-select">
                                <option value="">Select category</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            Add Product
                        </button>
                    </form>

                    <!-- Existing Products List -->
                    <h3 class="mt-4 mb-3">Existing Products</h3>
                    <div class="accordion" id="productsAccordion">
                        @foreach($groups as $group)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $group->id }}">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse{{ $group->id }}" 
                                            aria-expanded="false" 
                                            aria-controls="collapse{{ $group->id }}">
                                        {{ $group->name }} ({{ $group->items_count ?? 0 }})
                                    </button>
                                </h2>
                                <div id="collapse{{ $group->id }}" 
                                     class="accordion-collapse collapse" 
                                     aria-labelledby="heading{{ $group->id }}" 
                                     data-bs-parent="#productsAccordion">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            @foreach($group->items as $item)
                                                <li class="list-group-item">{{ $item->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed bottom-0 end-0 m-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show position-fixed bottom-0 end-0 m-3" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<style>
.nav-tabs {
    padding: 0.5rem 1rem 0;
    border-bottom: none;
}

.nav-tabs .nav-link {
    border: none;
    padding: 0.75rem 1rem;
    color: #6c757d;
    margin-right: 0.5rem;
    border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link:hover {
    border-bottom: 2px solid #dee2e6;
    color: #495057;
}

.nav-tabs .nav-link.active {
    border-bottom: 2px solid #198754;
    color: #198754;
    background: transparent;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>

@endsection