<!-- daily-sales.blade.php - Fixed version with proper null checking -->
<div class="card mb-4 daily-sales-card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fa-solid fa-chart-line mr-2"></i> Daily Sales
        </h5>
        <div class="date-selector">
            <div class="input-group">
                <input type="date" id="salesDate" class="form-control form-control-sm" 
                       value="{{ $data['selectedDate'] ?? date('Y-m-d') }}">
                <div class="input-group-append">
                    <button class="btn btn-light btn-sm" type="button" id="updateSalesBtn">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0" id="dailySalesContent">
        @if(empty($data['dailySales']['by_category']))
            <div class="text-center p-4">
                <i class="fa-solid fa-receipt text-muted" style="font-size: 2rem;"></i>
                <p class="mt-2 mb-0">No sales recorded for {{ \Carbon\Carbon::parse($data['selectedDate'] ?? date('Y-m-d'))->format('M d, Y') }}</p>
            </div>
        @else
            <div class="daily-sales-summary p-3 bg-light border-bottom">
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($data['selectedDate'] ?? date('Y-m-d'))->format('M d, Y') }} | 
                <strong>Total Items Sold:</strong> {{ $data['dailySales']['total_items'] ?? 0 }}
            </div>
            <div class="daily-sales-list">
                @foreach($data['dailySales']['by_category'] as $categoryId => $category)
                    <div class="category-section">
                        <div class="category-header d-flex justify-content-between align-items-center p-2 bg-light border-bottom">
                            <span class="font-weight-bold">{{ $category['name'] ?? 'Uncategorized' }}</span>
                            <span class="badge badge-primary">{{ $category['total'] ?? 0 }} items</span>
                        </div>
                        <ul class="list-group list-group-flush">
                            @foreach($category['items'] ?? [] as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <span class="item-name">{{ $item['name'] ?? 'Unknown' }}</span>
                                        <small class="text-muted d-block">by {{ $item['user'] ?? 'Unknown' }}</small>
                                    </div>
                                    <span class="badge badge-pill badge-secondary">{{ $item['quantity'] ?? 0 }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="card-footer text-center">
        <a href="#" class="btn btn-sm btn-outline-primary" id="printDailySalesBtn">
            <i class="fa-solid fa-print mr-1"></i> Print Report
        </a>
    </div>
</div>

<style>
.daily-sales-card {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.daily-sales-list {
    max-height: 500px;
    overflow-y: auto;
}

.category-section {
    border-bottom: 1px solid #eee;
}

.category-section:last-child {
    border-bottom: none;
}

.category-header {
    background-color: #f8f9fa;
}

.list-group-item {
    padding: 0.5rem 1rem;
    border-left: none;
    border-right: none;
}

.item-name {
    font-weight: 500;
}

.badge-pill {
    min-width: 30px;
}

#dailySalesContent {
    position: relative;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255,255,255,0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

.error-message {
    color: #dc3545;
    padding: 20px;
    text-align: center;
}

@media print {
    body * {
        visibility: hidden;
    }
    
    .daily-sales-card, .daily-sales-card * {
        visibility: visible;
    }
    
    .daily-sales-card {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    .card-footer {
        display: none;
    }
}
</style>

<script>
// Make sure jQuery is properly loaded before running this script
$(document).ready(function() {
    console.log('Daily sales script initialized');
    
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Update sales data when search button is clicked
    $('#updateSalesBtn').click(function() {
        updateDailySales();
    });
    
    // Also update when pressing Enter in the date field
    $('#salesDate').keypress(function(e) {
        if(e.which == 13) { // Enter key
            updateDailySales();
        }
    });
    
    // Function to update daily sales data
    function updateDailySales() {
        var selectedDate = $('#salesDate').val();
        
        // Validate date format
        if (!selectedDate || !selectedDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
            alert('Please select a valid date');
            return;
        }
        
        console.log('Searching for date:', selectedDate);
        
        // Show loading overlay
        $('#dailySalesContent').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        
        // Use direct page refresh - most reliable method
        window.location.href = '/inventory/stock?date=' + selectedDate;
    }
    
    // Handle print button
    $('#printDailySalesBtn').click(function(e) {
        e.preventDefault();
        window.print();
    });
});
</script>