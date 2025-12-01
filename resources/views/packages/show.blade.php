@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
            <div class="card-header bg-black text-white p-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Package Details</h5>
    
    <div class="d-flex gap-2"> <!-- Added div with gap for button spacing -->
        <a href="{{ route('packages.edit', $package) }}" class="btn btn-warning btn-sm">
            <i class="fas fa-edit me-1"></i> Edit Package
        </a>
        <a href="{{ route('packages.print', $package) }}" 
           class="btn btn-warning btn-sm" 
           target="_blank">
            <i class="fas fa-print me-1"></i> Print Details
        </a>
        <a href="{{ route('packages.index') }}" class="btn btn-outline-light btn-sm">
            Back to Packages
        </a>
    </div>
</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($package->image)
                                <img src="{{ asset('storage/' . $package->image) }}" 
                                     class="img-fluid rounded" 
                                     alt="{{ $package->name }}">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h3>{{ $package->name }}</h3>
                            <p class="text-muted">Category: {{ $package->category->name }}</p>
                            <div class="h4 text-primary mb-4">
                                Rs. {{ number_format($package->price, 2) }}
                            </div>
                            <div class="mb-4">
                                <h5>Description</h5>
                                <p>{{ $package->description }}</p>
                            </div>
                            @if($package->menu_items)
                            <div class="mb-4">
                                <h5>Menu Items</h5>
                                <ul class="list-group">
                                    @foreach($package->menu_items as $item)
                                        <li class="list-group-item">{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            @if($package->additional_info)
                            <div class="mb-4">
                                <h5>Additional Information</h5>
                                <ul class="list-group">
                                    @foreach($package->additional_info as $key => $value)
                                        <li class="list-group-item">
                                            <strong>{{ ucfirst($key) }}:</strong> {{ $value }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection