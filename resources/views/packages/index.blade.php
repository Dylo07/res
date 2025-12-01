@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card main-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Hotel Packages</h5>
            <div class="category-actions">
                <button type="button" class="btn btn-custom-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus-circle me-2"></i>Add Category
                </button>
                @if($categories->count() > 0)
                    <button type="button" class="btn btn-custom-primary" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                        <i class="fas fa-box-open me-2"></i>Add Package
                    </button>
                @endif
            </div>
        </div>
        
        <div class="card-body">
            @forelse($categories as $category)
                @if($category->packages->count() > 0)
                    <div class="category-section">
                        <div class="category-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $category->name }}</h5>
                            <div class="category-actions">
                                <button type="button" 
                                        class="btn btn-outline-dark btn-sm edit-category" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editCategoryModal"
                                        data-category="{{ json_encode($category) }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            @foreach($category->packages as $package)
                                <div class="col-md-4 mb-4">
                                    <div class="package-card">
                                        <div class="image-container">
                                            @if($package->image)
                                                <img src="{{ asset('storage/' . $package->image) }}" 
                                                     class="card-img-top" 
                                                     alt="{{ $package->name }}">
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $package->name }}</h5>
                                            <p class="card-text">{{ Str::limit($package->description, 100) }}</p>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <span class="price-tag">
                                                    <i class="fas fa-tag me-1"></i>
                                                    Rs. {{ number_format($package->price, 2) }}
                                                </span>
                                                <a href="{{ route('packages.show', $package) }}" 
                                                   class="btn btn-custom-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i> View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @empty
                <div class="empty-state">
                    <i class="fas fa-box-open mb-3"></i>
                    <h5>No Packages Available</h5>
                    <p class="text-muted">Start by adding a category and then create packages.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('package-categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Package Modal -->
<div class="modal fade" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('packages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Package Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price (Rs.)</label>
                            <input type="number" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Package Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Menu Items (Optional)</label>
                            <textarea name="menu_items" class="form-control" rows="4" 
                                    placeholder="Enter each item on a new line"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Additional Information (Optional)</label>
                            <textarea name="additional_info" class="form-control" rows="4" 
                                    placeholder="Enter in key: value format&#10;Example:&#10;Duration: 3 hours&#10;Max Guests: 10"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Package</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit category modal
    const editButtons = document.querySelectorAll('.edit-category');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = JSON.parse(this.dataset.category);
            const form = document.getElementById('editCategoryForm');
            form.action = `/package-categories/${category.id}`;
            document.getElementById('edit_name').value = category.name;
            document.getElementById('edit_description').value = category.description || '';
        });
    });

    // Form validation for the package form
    const packageForm = document.querySelector('#addPackageModal form');
    if (packageForm) {
        packageForm.addEventListener('submit', function(e) {
            const price = this.querySelector('input[name="price"]').value;
            if (price <= 0) {
                e.preventDefault();
                alert('Price must be greater than 0');
                return false;
            }
        });
    }

    // Image preview functionality
    const imageInput = document.querySelector('input[name="image"]');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                if (this.files[0].size > 2 * 1024 * 1024) { // 2MB
                    alert('Image size should not exceed 2MB');
                    this.value = '';
                    return;
                }
                
                const fileTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!fileTypes.includes(this.files[0].type)) {
                    alert('Please upload an image file (JPG, JPEG, PNG)');
                    this.value = '';
                    return;
                }
            }
        });
    }
});
</script>
@endpush

<style>
/* Card Styles */
.card {
    border: none;
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.card-header {
    background: linear-gradient(45deg, #000000, #1a1a1a);
    border-bottom: none;
    padding: 1.25rem;
}

.card-body {
    padding: 1.5rem;
}

/* Category Section Styles */
.category-section {
    background: #ffffff;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
}

.category-header {
    position: relative;
    margin-bottom: 25px;
    padding-bottom: 15px;
}

.category-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(45deg, #000000, #333333);
    border-radius: 2px;
}

/* Package Card Styles */
.package-card {
    height: 100%;
    border-radius: 12px;
    overflow: hidden;
    background: #ffffff;
    border: none;
    transition: all 0.3s ease;
}

.package-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.package-card .card-img-top {
    height: 200px;
    object-fit: cover;
    transition: all 0.3s ease;
}

.package-card:hover .card-img-top {
    transform: scale(1.05);
}

.package-card .card-body {
    padding: 1.25rem;
}

.package-card .card-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: #333;
}

.package-card .card-text {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.5;
}

/* Price Tag Styles */
.price-tag {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1.25rem;
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    display: inline-block;
}

/* Button Styles */
.btn-custom-primary {
    background: linear-gradient(45deg, #000000, #333333);
    border: none;
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-custom-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Category Actions */
.category-actions {
    gap: 10px;
    display: flex;
}

.category-actions .btn {
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

/* Alert Styles */
.alert {
    border-radius: 10px;
    border: none;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.alert-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
}

.alert-danger {
    background: linear-gradient(45deg, #dc3545, #c82333);
    color: white;
}

/* Modal Styles */
.modal-content {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(45deg, #000000, #1a1a1a);
    color: white;
    border-bottom: none;
}

.modal-footer {
    border-top: none;
    padding: 1.5rem;
}

/* Empty State Styles */
.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    border-radius: 12px;
}

.empty-state i {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .category-actions {
        flex-direction: column;
    }
    
    .category-actions .btn {
        width: 100%;
    }
    
    .package-card .card-img-top {
        height: 150px;
    }
}

/* Image Container Styles */
.image-container {
    position: relative;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
}

.image-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    padding: 20px;
    color: white;
    transition: all 0.3s ease;
}

/* Animation Effects */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.package-card {
    animation: fadeIn 0.5s ease-out;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection