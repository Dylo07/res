@extends('layouts.app')

@section('content')
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

<!-- Enhanced CSS for modern table styling -->
<style>
.order-panel-right {
    position: fixed;
    right: 20px;
    top: 100px;
    width: 700px;
    max-height: 80vh;
    overflow-y: auto;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    z-index: 1000;
}

.main-content-left {
    margin-right: 490px; /* Space for right panel */
}

/* Modern table grid styling */
.tables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
    gap: 10px;
    padding: 10px;
    background: white;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

/* Modern table cards */
.table-card {
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    min-height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.table-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    border-color: #3b82f6;
}

/* Available table styling */
.table-card.available {
    border-color: #10b981;
    background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
}

.table-card.available:hover {
    border-color: #059669;
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
}

/* Occupied table styling */
.table-card.occupied {
    border-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.table-card.occupied:hover {
    border-color: #d97706;
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.2);
}

/* Selected table styling */
.table-card.selected {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(59, 130, 246, 0.3);
}

.table-card.selected::after {
    content: '✓';
    position: absolute;
    top: -8px;
    right: -8px;
    background: #10b981;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.table-icon {
    font-size: 2rem;
    margin-bottom: 8px;
    color: #64748b;
}

.table-card.available .table-icon {
    color: #10b981;
}

.table-card.occupied .table-icon {
    color: #f59e0b;
}

.table-card.selected .table-icon {
    color: #3b82f6;
}

.table-name {
    font-weight: 700;
    font-size: 0.9rem;
    margin-bottom: 6px;
    color: #1e293b;
    line-height: 1.2;
}

.table-status {
    font-size: 0.7rem;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-status.bg-success {
    background-color: #10b981 !important;
    color: white;
}

.table-status.bg-warning {
    background-color: #f59e0b !important;
    color: white;
}

/* Categories styling */
.nav-tabs {
    border: none;
    background: white;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.nav-tabs .nav-link {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    margin: 3px;
    padding: 8px 16px;
    font-weight: 600;
    font-size: 0.85rem;
    color: #64748b;
    background: white;
    transition: all 0.2s ease;
}

.nav-tabs .nav-link:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    background: #f1f5f9;
    transform: translateY(-1px);
}

.nav-tabs .nav-link.active {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Search box styling */
.search-sec {
    margin-bottom: 20px;
}

.search-sec .form-control {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 15px;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.search-sec .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Menu items styling */
#list-menu {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.btn-menu {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 15px;
    margin: 8px;
    text-align: center;
    transition: all 0.2s ease;
    color: #1e293b;
    text-decoration: none;
    display: inline-block;
    min-height: 80px;
}

.btn-menu:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    text-decoration: none;
}

/* Order panel header */
.order-panel-right h6 {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    margin: -20px -20px 20px -20px;
    padding: 15px 20px;
    border-radius: 12px 12px 0 0;
    font-weight: 600;
    text-align: center;
}

/* Hide Tables button styling */
#btn-show-tables {
    background: #3b82f6;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    color: white;
    font-weight: 600;
    transition: all 0.2s ease;
    margin-bottom: 20px;
}

#btn-show-tables:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

@media (max-width: 1200px) {
    .order-panel-right {
        position: static;
        width: 100%;
        margin-top: 20px;
    }
    .main-content-left {
        margin-right: 0;
    }
    .tables-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 8px;
        padding: 15px;
    }
}

@media (max-width: 768px) {
    .tables-grid {
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
        gap: 6px;
        padding: 10px;
    }
    
    .table-card {
        padding: 12px;
        min-height: 80px;
    }
    
    .table-icon {
        font-size: 1.5rem;
        margin-bottom: 4px;
    }
    
    .table-name {
        font-size: 0.8rem;
    }
    
    .table-status {
        font-size: 0.6rem;
        padding: 2px 6px;
    }
}
</style>

<div class="container">
  <div class="main-content-left">
    <div class="row" id="table-detail"></div>
    <div class="row justify-content-center py5">
      <div class="col-md-12">
        <button tabindex="-2" class="btn btn-primary btn-block" id="btn-show-tables">View All Tables</button>
        
        <!-- Categories and menu in main area -->
        <div class="mt-3">
          <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              @foreach($categories as $category)
                <a class="nav-item nav-link btn-outline-success" data-id="{{ $category->id }}" data-toggle="tab">
                  {{ $category->name }}
                </a>
              @endforeach
            </div>
          </nav>
          <br>
          <div class="row search-sec">
            <div class="col-md-6"></div>
            <div class="col-md-6 pull-right">
              <input type="text" class="form-control" id="searchkeyword" placeholder="search menu" name="search">
            </div>
          </div>
          <div id="list-menu" class="row mt-2"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Order panel in fixed right position -->
<div class="order-panel-right">
  <h6><i class="fas fa-receipt me-2"></i>Current Order</h6>
  <div id="selected-table"></div>
  <div id="order-detail"></div>
</div>

<!-- Include only one version of jQuery (full version for AJAX support) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!-- Total Calculation Code -->
<script>
// Validation function for numeric inputs
function validateNumeric(value) {
    const num = parseFloat(value);
    return isNaN(num) ? 0 : num;
}

function updateTotalOrder() {
    let total = 0;
    let hasErrors = false;

    $('#order-detail table tbody tr').each(function() {
        const qtyInput = $(this).find('input.change-quantity');
        const priceCell = $(this).find('td:nth-child(3)');
        
        if (qtyInput.length > 0 && priceCell.length > 0) {
            const qty = validateNumeric(qtyInput.val());
            const price = validateNumeric(priceCell.text());
            
            // Validate reasonable ranges
            if (qty < 0 || qty > 1000 || price < 0 || price > 100000) {
                console.error('Invalid values detected:', { qty, price });
                hasErrors = true;
                return false; // Break the loop
            }
            
            const itemTotal = qty * price;
            total += itemTotal;
            
            // Update the total cell for this row
            $(this).find('td:nth-child(4)').text(itemTotal.toFixed(2));
        }
    });

    if (hasErrors) {
        console.error('Calculation stopped due to invalid values');
        return;
    }

    // Format and display total
    const formattedTotal = total.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    $('.totalAmount').text('Total Amount: Rs ' + formattedTotal);
    $('.btn-payment').attr('data-totalAmount', total.toFixed(2));
    
    // Log for debugging
    console.log('Total calculated:', total);
}

let updateTimeout;
function debouncedUpdate() {
    clearTimeout(updateTimeout);
    updateTimeout = setTimeout(function() {
        try {
            updateTotalOrder();
        } catch (error) {
            console.error('Error in total calculation:', error);
        }
    }, 150); // Slightly longer delay for stability
}

// Function to convert old table HTML to modern cards
function convertTablesToModernCards(htmlString) {
    const $temp = $('<div>').html(htmlString);
    let modernHtml = '<div class="tables-grid">';
    
    $temp.find('.btn-table').each(function() {
        const $btn = $(this);
        const tableId = $btn.data('id');
        const tableName = $btn.data('name');
        const $badge = $btn.find('.badge');
        const isAvailable = $badge.hasClass('badge-success');
        
        const statusClass = isAvailable ? 'available' : 'occupied';
        const statusText = isAvailable ? 'Available' : 'Occupied';
        const statusBg = isAvailable ? 'bg-success' : 'bg-warning';
        
        modernHtml += `
            <div class="table-card ${statusClass} btn-table" data-id="${tableId}" data-name="${tableName}">
                <div class="table-name">${tableName}</div>
                <div class="table-status ${statusBg}">${statusText}</div>
            </div>
        `;
    });
    
    modernHtml += '</div>';
    return modernHtml;
}
</script>

<!-- Payment Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Payment</h5>
        <button tabindex="-4" type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h3 class="totalAmount"></h3>
        <h3 class="changeAmount">Service Charge: </h3>
        <div tabindex="-1" class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Rs</span>
          </div>
          <input tabindex="-2" type="number" id="recieved-amount" class="form-control">
        </div>
        <div class="form-group">
          <label tabindex="-1" for="payment">Payment Type</label>
          <select tabindex="-3" class="form-control" id="payment-type">
            <option tabindex="-2" value="cash">Cash</option>
            <option value="credit card">Credit Card</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button tabindex="-1" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-save-payment">Save Payment</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
    // Attach event listeners for recalculating total
    $('#order-detail').on('change', '.change-quantity', function() {
        const qty = validateNumeric($(this).val());
        if (qty < 1) {
            $(this).val(1);
        }
        debouncedUpdate();
    });
    
    $('#order-detail').on('click', '.btn-delete-saledetail', debouncedUpdate);

    // Recalculate after AJAX responses
    $(document).ajaxComplete(function(event, xhr, settings) {
        if (settings.url && settings.url.includes('/cashier/')) {
            setTimeout(debouncedUpdate, 100);
        }
    });

    // Hide table details by default
    $("#table-detail").hide();

    window.onload = function() {
        if($("#table-detail").is(":hidden")){
            // Use relative URL to avoid HTTPS/HTTP conflicts
            $.get("/cashier/getTable", function(data){
                // Convert old table HTML to modern cards
                const modernTablesHtml = convertTablesToModernCards(data);
                $("#table-detail").html(modernTablesHtml);
                $("#table-detail").slideDown('fast');
                $("#btn-show-tables").html('Hide Tables').removeClass('btn-primary').addClass('btn-dark');
            }).fail(function(xhr, status, error) {
                console.error('Error loading tables:', error);
            });
        } else {
            $("#table-detail").slideUp('fast');
            $("#btn-show-tables").html('View All Tables').removeClass('btn-danger').addClass('btn-primary');
        }
        setTimeout(function(){
            $("#table-detail .table-card.available").first().click();
            // Load first category
            setTimeout(function() {
                $(".nav-link").first().click();
            }, 500);
        }, 1000);
    };

    // Search function
    $(document).on('keyup', '#searchkeyword', function(e) {
        if ($(this).val().length > 2) {  
            e.preventDefault();
            // Search across all categories when typing
            getmenuList(0);
        }
    });

    function getmenuList(id){
        var search_key = $("#searchkeyword").val().trim();
        // Use relative URL to avoid HTTPS/HTTP conflicts
        var url = "/cashier/getMenuByCategory/" + id;
        if (search_key) {
            url += "/" + search_key;
        }
        
        $.get(url, function(data){
            $("#list-menu").hide();
            $("#list-menu").html(data);
            $("#list-menu").fadeIn('fast');
        }).fail(function(xhr, status, error) {
            console.error('Error loading menu:', error);
            $("#list-menu").html('<div class="alert alert-danger">Error loading menu items. Please try again.</div>');
        });
    }

    // Load menus by category
    $(".nav-link").click(function(){
        $(".nav-link").removeClass('active');
        $(this).addClass('active');
        var id = $(this).data("id");
        $("#searchkeyword").val('');
        getmenuList(id);
    });

    var SELECTED_TABLE_ID = "";
    var SELECTED_TABLE_NAME = "";
    var SALE_ID = "";

    // Show sale details when a table is selected (updated for modern cards)
    $(document).on("click", ".btn-table", function(){
        SELECTED_TABLE_ID = $(this).data("id");
        SELECTED_TABLE_NAME = $(this).data("name");
        
        // Update visual selection for modern cards
        $(".table-card").removeClass('selected');
        $(this).addClass('selected');
        
        $("#selected-table").html('<div class="alert alert-info"><strong>Table: ' + SELECTED_TABLE_NAME + '</strong></div>');
        // Use relative URL to avoid HTTPS/HTTP conflicts
        $.get("/cashier/getSaleDetailsByTable/" + SELECTED_TABLE_ID, function(data){
            $("#order-detail").html(data);
        }).fail(function(xhr, status, error) {
            console.error('Error loading sale details:', error);
            $("#order-detail").html('<div class="alert alert-danger">Error loading order details. Please try again.</div>');
        });
    });

    $("#list-menu").on("click", ".btn-menu", function(){
        if (SELECTED_TABLE_ID == "") {
            alert("You need to select a table for the customer first");
        } else {
            var menu_id = $(this).data("id");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "menu_id": menu_id,
                    "table_id": SELECTED_TABLE_ID,
                    "table_name": SELECTED_TABLE_NAME,
                    "quantity": 1
                },
                url: "/cashier/orderFood",
                success: function(response){
                    // Update the order details
                    $("#order-detail").html(response.html);
                    
                    // Update table status for modern cards
                    if (response.tableStatusChanged) {
                        var $tableCard = $(`.table-card[data-id="${response.tableId}"]`);
                        $tableCard.removeClass('available').addClass('occupied');
                        $tableCard.find('.table-status').removeClass('bg-success').addClass('bg-warning').text('Occupied');
                        $tableCard.find('.table-icon').css('color', '#f59e0b');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    });

    $("#order-detail").on('click', ".btn-confirm-order", function(){
        var SaleID = $(this).data("id");
        $.ajax({
            type: "POST",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "sale_id": SaleID
            },
            url: "/cashier/confirmOrderStatus",
            success: function(data){
                $("#order-detail").html(data);
            }
        });
    });

    // Delete sale detail
    $("#order-detail").on("click", ".btn-delete-saledetail", function(){
        var saleDetailID = $(this).data("id");
        $.ajax({
            type: "POST",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "saleDetail_id": saleDetailID
            },
            url: "/cashier/deleteSaleDetail",
            success: function(data){
                $("#order-detail").html(data);
            }
        });
    });

    // Increase quantity
    $("#order-detail").on("click", ".btn-increase-quantity", function(){
        var saleDetailID = $(this).data("id");
        $.ajax({
            type: "POST",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "saleDetail_id": saleDetailID
            },
            url: "/cashier/increase-quantity",
            success: function(data){
                $("#order-detail").html(data);
            }
        });
    });

    // Change quantity
    $("#order-detail").on("change", ".change-quantity", function(){
        var saleDetailID = $(this).data("id");
        var qty = Number($(this).val());
        if(qty < 1){
            $(this).val(1);
            qty = 1;
        }
        $.ajax({
            type: "POST",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "saleDetail_id": saleDetailID,
                "qty": qty
            },
            url: "/cashier/change-quantity",
            success: function(data){
                $("#order-detail").html(data);
            }
        });
    });

    // Decrease quantity
    $("#order-detail").on("click", ".btn-decrease-quantity", function(){
        var saleDetailID = $(this).data("id");
        $.ajax({
            type: "POST",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "saleDetail_id": saleDetailID
            },
            url: "/cashier/decrease-quantity",
            success: function(data){
                $("#order-detail").html(data);
            }
        });
    });

    // Print KOT
    $("#order-detail").on("click", ".printKot", function(){
        var saleID = $(this).data('id');
        $.ajax({
            type: "POST",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "saleID": saleID
            },
            url: "/cashier/printOrder",
            success: function(data){
                window.open(data, '_blank').focus();
            }
        });
    });

    // Payment button click
    $("#order-detail").on("click", ".btn-payment", function(){
        var totalAmount = $(this).attr('data-totalAmount');
        $(".totalAmount").html("Total Amount Rs " + totalAmount);
        $("#recieved-amount").val(0);
        SALE_ID = $(this).data('id');
    });

    // Calculate change amount on keyup
    $("#recieved-amount").keyup(function(){
        const totalAmount = parseFloat($(".btn-payment").attr('data-totalAmount')) || 0;
        const receivedAmount = validateNumeric($(this).val());
        
        // Service charge is typically the received amount
        const serviceCharge = receivedAmount;
        
        $(".changeAmount").html("Service Charge: Rs " + serviceCharge.toFixed(2));
        
        // Enable/disable save button based on reasonable input
        $('.btn-save-payment').prop('disabled', receivedAmount < 0);
    });

    // Save payment
    $(".btn-save-payment").click(function(){
        var recievedAmount = $("#recieved-amount").val();
        var paymentType = $("#payment-type").val();
        var saleId = SALE_ID;
        $.ajax({
            type: "POST",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "saleID": saleId,
                "recievedAmount": recievedAmount,
                "PaymentType": paymentType
            },
            url: "/cashier/savePayment",
            success: function(data){
                window.location.href = data;
            }
        });
    });
});
</script>
@endsection