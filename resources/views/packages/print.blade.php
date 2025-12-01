<!-- Create this as print.blade.php in resources/views/packages/ -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $package->name }} - Details</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        .hotel-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .package-name {
            font-size: 20px;
            color: #000;
            margin-bottom: 20px;
        }

        .details-section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            color: #000;
        }

        .price {
            font-size: 18px;
            color: #000;
            margin: 20px 0;
        }

        .menu-items {
            margin-bottom: 20px;
        }

        .menu-items ul {
            list-style-type: none;
            padding-left: 0;
        }

        .additional-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #666;
            padding: 10px 0;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="hotel-name">Hotel Soba Lanka</div>
        <div class="package-name">{{ $package->name }}</div>
    </div>

    <div class="details-section">
        <div class="section-title">Category</div>
        <div>{{ $package->category->name }}</div>
    </div>

    <div class="price">
        <div class="section-title">Price</div>
        Rs. {{ number_format($package->price, 2) }}
    </div>

    <div class="details-section">
        <div class="section-title">Description</div>
        <div>{{ $package->description }}</div>
    </div>

    @if($package->menu_items)
    <div class="menu-items">
        <div class="section-title">Menu Items</div>
        <ul>
            @foreach($package->menu_items as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($package->additional_info)
    <div class="additional-info">
        <div class="section-title">Additional Information</div>
        <ul>
            @foreach($package->additional_info as $key => $value)
                <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="footer">
        Printed on: {{ date('Y-m-d H:i:s') }}
    </div>

    <button class="no-print" onclick="window.print()" style="position: fixed; top: 20px; right: 20px; padding: 10px 20px;">
        Print Now
    </button>
</body>
</html>