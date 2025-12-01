<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 40px;
            color: #333;
        }
        .bill-to-table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }
    
    .bill-to-table th {
        background-color: #2ea022;
        color: white;
        padding: 8px;
        text-align: left;
        font-weight: normal;
    }
    
    .bill-to-table td {
        padding: 8px;
        border: 1px solid #ddd;
    }

    .quotation-header {
        width: 100%;
        margin-bottom: 20px;
    }
        .logo {
            width: 100px; /* Made logo slightly smaller */
            height: auto;
        }

        .company-info {
            position: absolute;
            right: 0;
            top: 0;
            text-align: right;
        }
        
        .company-info h2 {
            margin: 0 0 5px;
            color: #2ea022;
            font-size: 16px; /* Made company name smaller */
        }
        
        .contact-info {
            font-size: 12px; /* Made contact info smaller */
            line-height: 1.4;
        }

        .header {
            margin-bottom: 40px;
            position: relative;
            height: 120px; /* Fixed height for header */
        }
        .header h2 {
            margin: 15px 0 10px;
            color: #2ea022; /* Brand green color */
        }
        .info-section {
            margin-bottom: 30px;
        }
        .quotation-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #2ea022; /* Brand green color */
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: 500;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }
        .amount-column {
            text-align: right;
        }
        .totals-row td {
            border-top: 2px solid #2ea022;
            font-weight: bold;
        }
        .comments-section {
            margin-top: 40px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .comments-section h4 {
            color: #2ea022;
            margin-top: 0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 0.9em;
        }
        .contact-info {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
        <img src="{{asset('image/Holidaym.png')}}" alt="Soba Lanka Logo" class="logo">
        
    </div>
        <div class="company-info">
            <h2>Soba Lanka Holiday Resort (PVT) LTD</h2>
            <div class="contact-info">
                Balawattala Road, Meliripura<br>
                071 71 52 955<br>
                sobalanka.com<br>
                sobalankahotel@gmail.com
            </div>
        </div>
    </div>
    <table class="bill-to-table">
    <tr>
        <th colspan="2" style="text-align: left; padding-right: 2px;">
             Date: {{ $quotation->quotation_date->format('jS \o\f F, Y') }}
        </th>
    </tr>
    </table>
    <table class="bill-to-table">
        
    <tr>
        <th colspan="2">Bill To:</th>
    </tr>
    <tr>
        <td width="20%">Client Name</td>
        <td>{{ $quotation->client_name }}</td>
    </tr>
    <tr>
        <td>Schedule</td>
        <td>{{ $quotation->schedule->format('jS \o\f F, Y') }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th style="width: 40%;">Description</th>
            <th style="width: 30%; text-align: right;">Price Per Item </th>
            <th style="width: 15%; text-align: right;">Pax</th>
            <th style="width: 15%; text-align: right;">Quantity</th>
            <th style="width: 15%; text-align: right;">Amount </th>
        </tr>
    </thead>
    <tbody>
    <tbody>
    @foreach($quotation->items as $item)
    <tr>
        <td>{{ $item['description'] ?? '' }}</td>
        <td class="amount-column">
            {{ isset($item['pricePerItem']) ? $item['pricePerItem'] : '0.00' }}
        </td>
        <td class="amount-column">
            {{ isset($item['pax']) ? $item['pax'] : '0' }}
        </td>
        <td class="amount-column">
            {{ isset($item['quantity']) ? $item['quantity'] : '0' }}
        </td>
        <td class="amount-column">
            {{ isset($item['amount']) ? $item['amount'] : '0.00' }}
        </td>
        
    </tr>
    @endforeach
    <tr>
        <td colspan="4" class="amount-column">Service Charge</td>
        <td class="amount-column">{{ number_format((float)$quotation->service_charge, 2) }}</td>
    </tr>
    <tr class="totals-row">
        <td colspan="4" class="amount-column">Grand Total (LKR)</td>
        <td class="amount-column">{{ number_format((float)$quotation->total_amount, 2) }}</td>
    </tr>
</tbody>
    </table>

    <div class="comments-section">
        <h4>Other Comments:</h4>
        <ul style="list-style-type: none; padding-left: 0;">
            @foreach($quotation->comments as $comment)
            <li style="margin-bottom: 8px;">• {{ $comment }}</li>
            @endforeach
        </ul>
    </div>

    <div class="footer">
        <p>If you have any questions about this quotation, please contact us.</p>
        <p>This is a computer-generated quotation; therefore, no signature is required.</p>
        <p style="color: #2ea022; font-weight: bold;">Thank you !</p>
    </div>
</body>
</html>