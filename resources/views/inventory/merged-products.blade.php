@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content">
        @include('inventory.inc.sidebar')
 
        <div class="col-md-8">
            <i class="fa-solid fa-object-group"></i> Manage Merged Products 
            <hr>
            
            @if(Session()->has('status'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert"></button>
                {{Session()->get('status')}}
            </div>
            @endif

            @if(Session()->has('error'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert"></button>
                {{Session()->get('error')}}
            </div>
            @endif
            
            <h3>Merged Product Groups</h3>
            @if(count($mergedGroups) > 0)
                @foreach($mergedGroups as $parentId => $group)
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>{{ $group['parent']->name }} (Parent)</h4>
                            <div>
                                <a href="{{ route('merged-products.consolidate', $parentId) }}" class="btn btn-success btn-sm">Consolidate Stock</a>
                                <a href="{{ route('merged-products.unmerge', $parentId) }}" class="btn btn-danger btn-sm">Unmerge</a>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#redistributeModal{{ $parentId }}">Redistribute</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Current Stock</th>
                                        <th>ML Value</th>
                                        <th>Total ML</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $group['parent']->name }}</td>
                                        <td>{{ $group['parent']->stock }}</td>
                                        <td>
                                            @php
                                                preg_match('/\((\d+)\s*ml\)/i', $group['parent']->name, $matches);
                                                $parentMl = isset($matches[1]) ? $matches[1] : 'N/A';
                                                $parentTotalMl = $parentMl !== 'N/A' ? $parentMl * $group['parent']->stock : 0;
                                            @endphp
                                            {{ $parentMl }} ml
                                        </td>
                                        <td>{{ $parentTotalMl }} ml</td>
                                    </tr>
                                    @foreach($group['children'] as $child)
                                        <tr>
                                            <td>{{ $child->name }}</td>
                                            <td>{{ $child->stock }}</td>
                                            <td>
                                                @php
                                                    preg_match('/\((\d+)\s*ml\)/i', $child->name, $matches);
                                                    $childMl = isset($matches[1]) ? $matches[1] : 'N/A';
                                                    $childTotalMl = $childMl !== 'N/A' ? $childMl * $child->stock : 0;
                                                @endphp
                                                {{ $childMl }} ml
                                            </td>
                                            <td>{{ $childTotalMl }} ml</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-info">
                                        <td colspan="3"><strong>Total ML</strong></td>
                                        <td>
                                            <strong>
                                                @php
                                                    $totalMl = $parentTotalMl;
                                                    foreach($group['children'] as $child) {
                                                        preg_match('/\((\d+)\s*ml\)/i', $child->name, $matches);
                                                        $childMl = isset($matches[1]) ? $matches[1] : 0;
                                                        $totalMl += $childMl * $child->stock;
                                                    }
                                                @endphp
                                                {{ $totalMl }} ml
                                            </strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Redistribute Modal -->
                    <div class="modal fade" id="redistributeModal{{ $parentId }}" tabindex="-1" role="dialog" aria-labelledby="redistributeModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="redistributeModalLabel">Redistribute Stock - {{ $group['parent']->name }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('merged-products.redistribute') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $parentId }}">
                                    <div class="modal-body">
                                        <p>Total ML Available: <strong>{{ $totalMl }} ml</strong></p>
                                        <p>Distribute to:</p>
                                        
                                        <div class="form-group">
                                            <label>{{ $group['parent']->name }}</label>
                                            <input type="number" class="form-control" name="distribution[0][ml_amount]" placeholder="ML amount" min="0" max="{{ $totalMl }}">
                                            <input type="hidden" name="distribution[0][menu_id]" value="{{ $parentId }}">
                                        </div>
                                        
                                        @foreach($group['children'] as $index => $child)
                                            <div class="form-group">
                                                <label>{{ $child->name }}</label>
                                                <input type="number" class="form-control" name="distribution[{{ $index + 1 }}][ml_amount]" placeholder="ML amount" min="0" max="{{ $totalMl }}">
                                                <input type="hidden" name="distribution[{{ $index + 1 }}][menu_id]" value="{{ $child->id }}">
                                            </div>
                                        @endforeach
                                        
                                        <div class="alert alert-warning">
                                            Note: The total distributed ML should not exceed {{ $totalMl }} ml.
                                            Stock will be adjusted based on the ML value of each product.
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    No merged product groups found. Create a new group below.
                </div>
            @endif
            
            <h3 class="mt-4">Create New Merged Group</h3>
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('merged-products.merge') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="productGroup">Select Product Group:</label>
                            <select id="productGroup" class="form-control">
                                <option value="">-- Select Product Base --</option>
                                @foreach($groupedProducts as $baseName => $products)
                                    <option value="{{ $baseName }}">{{ $baseName }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div id="productSelectionGroup" style="display: none;">
                            <div class="form-group">
                                <label for="parentProduct">Select Parent Product:</label>
                                <select id="parentProduct" name="parent_id" class="form-control" required>
                                    <option value="">-- Select Parent --</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Select Child Products:</label>
                                <div id="childProductsList" class="ml-4">
                                    <!-- Child products will be dynamically inserted here -->
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Merge Products</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#productGroup').change(function() {
            var baseName = $(this).val();
            if (baseName) {
                // Clear previous options
                $('#parentProduct').empty().append('<option value="">-- Select Parent --</option>');
                $('#childProductsList').empty();
                
                // Get products for this base name
                @foreach($groupedProducts as $baseName => $products)
                    if ('{{ $baseName }}' === baseName) {
                        // Add options for parent product
                        @foreach($products as $product)
                            $('#parentProduct').append('<option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock }})</option>');
                        @endforeach
                    }
                @endforeach
                
                $('#productSelectionGroup').show();
            } else {
                $('#productSelectionGroup').hide();
            }
        });
        
        $('#parentProduct').change(function() {
            var parentId = $(this).val();
            var baseName = $('#productGroup').val();
            
            // Clear previous child products
            $('#childProductsList').empty();
            
            if (parentId) {
                // Get products for this base name
                @foreach($groupedProducts as $baseName => $products)
                    if ('{{ $baseName }}' === baseName) {
                        // Add checkboxes for child products
                        @foreach($products as $product)
                            if ('{{ $product->id }}' !== parentId) {
                                var checkbox = '<div class="form-check">' +
                                    '<input class="form-check-input" type="checkbox" name="child_ids[]" value="{{ $product->id }}" id="child{{ $product->id }}">' +
                                    '<label class="form-check-label" for="child{{ $product->id }}">' +
                                    '{{ $product->name }} (Stock: {{ $product->stock }})' +
                                    '</label>' +
                                    '</div>';
                                $('#childProductsList').append(checkbox);
                            }
                        @endforeach
                    }
                @endforeach
            }
        });
    });
</script>
@endpush
@endsection