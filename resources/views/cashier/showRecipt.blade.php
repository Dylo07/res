<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restuarant App - Recipt - SaleID : {{$sale->id}}</title>
    <link type="text/css" rel="stylesheet" href="{{asset('css/recipt.css')}}"
    media="all" >
    
    <link type="text/css" rel="stylesheet" href="{{asset('css/no-print.css')}}"
    media="print" >
    
</head>
<body>
    <div id="wrapper">
        <div id="recipt-header">
        <p style="text-align:center;"><img width="200px" src="{{asset('image/lg.png')}}" alt="Logo"></p>
                       <p style="text-align:center;"> Balawattala Road, Melsiripura, Kurunegala</p>
            <p style="text-align:center;">| Restaurant | Swimming Pool | Cottages | Bar |</p>
            <p style="text-align:center;">Tel: 037 2250 308 | 071 7152 955</p>
            <p>Invoice No: <strong>{{$sale->id}}</strong></p>
                              
            
            <p > Date: <strong>{{$sale->updated_at}}</strong></p> 
            
            
        </div>
        <div id="recipt-body"></div>
        <table class="tb-sale-detail">
        <thead>
            <tr>
                
                <th>Menu</th>
                <th>Qty</th>
                <th>Price</th>
                <th style="text-align:right;">Total</th>
            </tr>


        </thead>
        <tbody>
            @foreach($saleDetails as $saleDetail)
            <tr>
               
                <td width="180" style="text-align:left;">{{$saleDetail->menu_name}}</td>
                <td width="50">{{$saleDetail->quantity}}</td>
                <td width="55">{{$saleDetail->menu_price}}</td>
                <td width="65" style="text-align:right;">{{$saleDetail->menu_price*$saleDetail->quantity}}</td>
                


            </tr>



            @endforeach



        </tbody>
        </table>
        <table class="tb-sale-total">
            <tbody>
                <tr>
                       
                </tr>
                <tr>
                    <td colspan="2">Total Quantity</td>
                    <td colspan="2"> {{$saleDetails->count()}}</td>
                </tr>
                <tr>
                    <td colspan="2">Payment Type</td>
                    <td colspan="2"> {{$sale->payment_type}}</td>
                </tr>
                <tr>
                    <td colspan="2">Total</td>
                    <td colspan="2"> Rs {{number_format($sale->total_price, 2)}}</td>
                </tr>
                <tr>
                    <td colspan="2">Service Charge</td>
                    <td colspan="2">Rs {{number_format($sale->total_recieved, 2)}}</td>
                </tr>
                <tr>
                    <td colspan="2">Net Amount</td>
                    <td colspan="2">Rs {{number_format($sale->change, 2)}}</td>
                </tr>
                
            </tbody>


        </table>
        <div id="recipt-footer">
        <p> ස්තූතීයි, නැවත එන්න !!</p>
        <p> THANK YOU, COME AGAIN !!</p>

        </div>
        <div id="buttons">
            
        <button class="btn btn-print" type="button" onclick="window.print(); return false;">
                Print
            </button>
        
        <a href="/cashier">
            <button class="btn btn-back">
                Back to cashier
            </button>
            </a>
            
           


        </div>
        
    </div>
</body>
</html>