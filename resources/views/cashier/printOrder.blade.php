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
<p style="text-align:center;font-size:20px;"> - KOT -</p>
    <div id="wrapper">
        <div id="recipt-header">
            <p style="font-size:20px;" >Invoice No: <strong>{{$sale->id}}</strong></p>
            <p style="font-size:20px;"> Date: <strong>{{$sale->updated_at}}</strong></p> 
        </div>
        <div id="recipt-body"></div>
        <table class="tb-sale-detail">
        <thead>
            <tr>
                <th style="font-size:20px;" >Menu</th>
                <th style="font-size:20px;">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($saleDetails as $saleDetail)
            <tr>
                <td width="180"  style="text-align:left;font-size:20px;">{{$saleDetail->menu_name}}</td>
                <td  width="100" style="font-size:20px;">{{$saleDetail->quantity}}</td>
                
            </tr>
            
            @endforeach
        </tbody>
        </table>
        
        <p style="font-size:20px;">_________________________</p>
        <div id="buttons">
            <a href="/cashier">
            <button tabindex ="-1" class="btn btn-back">
                Back to cashier
            </button>
            </a>
            <button class="btn btn-print" type="button" onclick="window.print(); return false;">
                Print
            </button>
        </div>
    </div>
</body>
</html>