@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        @if($errors->any())
          <div class="alert alert-danger">
              <ul>
                @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
              </ul>
          </div>
        @endif
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/home">Main Functions</a></li>
            <li class="breadcrumb-item"><a href="/report">Report</a></li>
            <li class="breadcrumb-item active" aria-current="page">Result</li>
          </ol>
        </nav>
      </div>
    </div>
    <!-- Current Viewing Date Range -->
    <div class="alert alert-info mb-3" role="alert">
      <h5 class="mb-0"><i class="fa fa-calendar"></i> Currently Viewing: <strong>{{ request('dateStart', date('m/d/Y')) }}</strong> to <strong>{{ request('dateEnd', date('m/d/Y')) }}</strong></h5>
    </div>

    <div class="row">
      <form action="/report/show" method="GET">
        <div class="col-md-12">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Choose Start Date For Report</label>
                <div class="input-group date" id="date-start" data-target-input="nearest">
                      <input type="text" name="dateStart" class="form-control datetimepicker-input" data-target="#date-start" value="{{ request('dateStart', date('m/d/Y')) }}"/>
                      <div class="input-group-append" data-target="#date-start" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar" style="font-size:25px;"></i></div>
                      </div>
                  </div>
              </div>  
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Choose End Date For Report</label>
                <div class="input-group date" id="date-end" data-target-input="nearest">
                    <input type="text" name="dateEnd" class="form-control datetimepicker-input" data-target="#date-end" value="{{ request('dateEnd', date('m/d/Y')) }}"/>
                    <div class="input-group-append" data-target="#date-end" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar" style="font-size:25px;"></i></div>
                    </div>
                </div>
              </div>    
            </div>
  
          </div>
          <br>

          <input class="btn btn-primary" type="submit" value="Show Report">
        
        </div>
      </form>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
          @if($sales->count() > 0)
            <!-- Summary Cards at Top -->
            <div class="row mb-4">
              <div class="col-md-4">
                <div class="card text-white bg-success">
                  <div class="card-body">
                    <h5 class="card-title">Total Sales</h5>
                    <h2 class="mb-0">Rs {{number_format($totalSale, 2)}}</h2>
                    <small>Period total revenue</small>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card text-white bg-info">
                  <div class="card-body">
                    <h5 class="card-title">Service Charge</h5>
                    <h2 class="mb-0">Rs {{number_format($serviceCharge, 2)}}</h2>
                    <small>Total S/C collected</small>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card text-white bg-primary">
                  <div class="card-body">
                    <h5 class="card-title">Total Receipts</h5>
                    <h2 class="mb-0">{{$sales->total()}}</h2>
                    <small>Number of transactions</small>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Suspicious Bills Section -->
            @if($suspiciousBills->count() > 0)
            <div class="card mb-4 border-warning">
              <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                  <i class="fas fa-exclamation-triangle"></i> Suspicious Bills (Price Mismatch)
                  <span class="badge badge-danger">{{$suspiciousBills->count()}} Found</span>
                </h4>
                <small>Items billed at different price than current menu price - possible fraud!</small>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped table-hover table-sm">
                    <thead class="thead-light">
                      <tr>
                        <th>Receipt #</th>
                        <th>Date/Time</th>
                        <th>Item</th>
                        <th class="text-right">Billed Price</th>
                        <th class="text-right">Current Price</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Difference</th>
                        <th class="text-right">Total Loss/Gain</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                        $totalDifference = 0;
                      @endphp
                      @foreach($suspiciousBills as $bill)
                      <tr class="{{ $bill->price_difference < 0 ? 'table-danger' : 'table-info' }}">
                        <td><strong>#{{$bill->sale_id}}</strong></td>
                        <td><small>{{date('m/d/Y H:i', strtotime($bill->updated_at))}}</small></td>
                        <td>{{$bill->menu_name}}</td>
                        <td class="text-right">
                          <span class="badge badge-warning">Rs {{number_format($bill->billed_price, 2)}}</span>
                        </td>
                        <td class="text-right">Rs {{number_format($bill->current_price, 2)}}</td>
                        <td class="text-right">{{$bill->quantity}}</td>
                        <td class="text-right">
                          @if($bill->price_difference < 0)
                            <span class="text-danger"><strong>-Rs {{number_format(abs($bill->price_difference), 2)}}</strong></span>
                          @else
                            <span class="text-success">+Rs {{number_format($bill->price_difference, 2)}}</span>
                          @endif
                        </td>
                        <td class="text-right">
                          @if($bill->total_difference < 0)
                            <span class="text-danger"><strong>-Rs {{number_format(abs($bill->total_difference), 2)}}</strong></span>
                          @else
                            <span class="text-success">+Rs {{number_format($bill->total_difference, 2)}}</span>
                          @endif
                        </td>
                      </tr>
                      @php
                        $totalDifference += $bill->total_difference;
                      @endphp
                      @endforeach
                      <tr class="table-dark">
                        <td colspan="7" class="text-right"><strong>Total Discrepancy:</strong></td>
                        <td class="text-right">
                          <strong class="{{ $totalDifference < 0 ? 'text-danger' : 'text-success' }}">
                            @if($totalDifference < 0)
                              -Rs {{number_format(abs($totalDifference), 2)}}
                            @else
                              +Rs {{number_format($totalDifference, 2)}}
                            @endif
                          </strong>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="alert alert-info mt-3">
                  <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
                  Red rows indicate items sold below current price (potential revenue loss). 
                  Check <a href="{{ route('menu.activity-log') }}" target="_blank">Menu Activity Log</a> to see who changed prices.
                </div>
              </div>
            </div>
            @endif

            <!-- Individual Receipts -->
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Individual Receipts ({{$sales->total()}})</h4>
              </div>
              <div class="card-body p-0">
                <table class="table table-hover mb-0">
                  <thead class="thead-light">
                    <tr>
                      <th width="5%">#</th>
                      <th width="10%">Receipt ID</th>
                      <th width="20%">Date & Time</th>
                      <th width="15%">Table</th>
                      <th width="15%">Staff</th>
                      <th width="15%" class="text-right">Bill Amount</th>
                      <th width="10%" class="text-right">S/C</th>
                      <th width="10%" class="text-right">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php 
                      $countSale = ($sales->currentPage() - 1) * $sales->perPage() + 1;
                    @endphp 
                    @foreach($sales as $sale)
                      <tr data-toggle="collapse" data-target="#receipt-{{$sale->id}}" class="clickable-row" style="cursor: pointer;">
                        <td>{{$countSale++}}</td>
                        <td><strong>{{$sale->id}}</strong></td>
                        <td>{{date("m/d/Y H:i", strtotime($sale->updated_at))}}</td>
                        <td>{{$sale->table_name}}</td>
                        <td>{{$sale->user_name}}</td>
                        <td class="text-right">Rs {{number_format($sale->total_price, 2)}}</td>
                        <td class="text-right">Rs {{number_format($sale->total_recieved, 2)}}</td>
                        <td class="text-right"><strong>Rs {{number_format($sale->change, 2)}}</strong></td>
                      </tr>
                      <tr class="collapse" id="receipt-{{$sale->id}}">
                        <td colspan="8" class="bg-light">
                          <div class="p-3">
                            <h6 class="text-muted mb-2">Items Ordered:</h6>
                            <table class="table table-sm table-bordered mb-0">
                              <thead>
                                <tr>
                                  <th>Item Name</th>
                                  <th class="text-center">Quantity</th>
                                  <th class="text-right">Unit Price</th>
                                  <th class="text-right">Total</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($sale->saleDetails as $saleDetail)
                                  <tr>
                                    <td>{{$saleDetail->menu_name}}</td>
                                    <td class="text-center">{{$saleDetail->quantity}}</td>
                                    <td class="text-right">Rs {{number_format($saleDetail->menu_price, 2)}}</td>
                                    <td class="text-right">Rs {{number_format($saleDetail->menu_price * $saleDetail->quantity, 2)}}</td>
                                  </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
   
            <div class="mt-3">
              {{$sales->appends($_GET)->links()}}
            </div>
            
            <!-- Sales Summary by Item -->
            <div class="card mb-4 mt-4">
              <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Items Sold Summary</h4>
              </div>
              <div class="card-body">
                <table class="table table-hover mb-0">
                  <thead class="thead-dark">
                    <tr>
                      <th>Category</th>
                      <th>Item Name</th>
                      <th class="text-right">Quantity</th>
                      <th class="text-right">Unit Price</th>
                      <th class="text-right">Total Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php 
                      $CategoryNew='';
                      $grandTotal = 0;
                    @endphp
                    @foreach($summarySales as $item)
                      @if ($CategoryNew != $item->name)
                        <tr class="bg-light">
                          <td colspan="5"><strong>{{$item->name}}</strong></td>
                        </tr>
                        @php 
                          $CategoryNew= $item->name;
                        @endphp
                      @endif
                      <tr>
                        <td></td>
                        <td>{{$item->menu_name}}</td>
                        <td class="text-right"><strong>{{$item->qty_sum}}</strong></td>
                        <td class="text-right">Rs {{number_format($item->avg_price, 2)}}</td>
                        <td class="text-right"><strong>Rs {{number_format($item->total_price, 2)}}</strong></td>
                      </tr>
                      @php
                        $grandTotal += $item->total_price;
                      @endphp
                    @endforeach
                    <tr class="table-dark">
                      <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                      <td class="text-right"><strong>Rs {{number_format($grandTotal, 2)}}</strong></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>


            
          @else
            <div class="alert alert-danger" role="alert">
              There is no Sale Report
            </div>
          @endif
        </div>
    </div>
  </div>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" integrity="sha512-3JRrEUwaCkFUBLK1N8HehwQgu8e23jTH4np5NHOmQOobuC4ROQxFwFgBLTnhcnQRMs84muMh0PnnwXlPq5MGjg==" crossorigin="anonymous" />
  
  <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" integrity="sha512-k6/Bkb8Fxf/c1Tkyl39yJwcOZ1P4cRrJu77p83zJjN2Z55prbFHxPs9vN7q3l3+tSMGPDdoH51AEU8Vgo1cgAA==" crossorigin="anonymous"></script>
  
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  
  <style>
    .clickable-row:hover {
      background-color: #f8f9fa !important;
    }
    .card {
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      border-radius: 8px;
    }
    .card-header h4 {
      font-weight: 600;
    }
  </style>
  
  <script type="text/javascript">
      $(function () {
          $('#date-start').datetimepicker({
            format : 'L'
          });
          $('#date-end').datetimepicker({
            format : 'L'
          });
          
          // Add click hint for expandable rows
          $('.clickable-row').attr('title', 'Click to view order details');
      });
  </script>

@endsection
         