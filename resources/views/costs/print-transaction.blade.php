<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details - {{ $cost->cost_date->format('F j, Y') }}</title>
    <style>
        @media print {
            #buttons { display: none; }
            @page { margin: 0.5cm; }
        }

        body { 
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            max-width: 400px; /* Narrower width to match image */
            margin: 0 auto;
        }
        
        #wrapper {
            position: relative;
            padding: 20px;
        }

        .background-logo {
            position: fixed;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
            width: 200px;
            height: auto;
        }

        #receipt-header {
            text-align: left;
            margin-bottom: 30px;
        }

        #receipt-header h2 {
            margin-bottom: 5px;
        }

        .transaction-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            background: transparent;
        }

        .transaction-details tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .transaction-details th,
        .transaction-details td {
            padding: 8px 0;
            background: transparent !important;
            vertical-align: top;
            line-height: 1.4;
        }

        .transaction-details th {
            text-align: left;
            width: 100px;
            font-weight: normal;
        }

        /* Signature styles */
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin: 50px 0 30px;
            padding-top: 20px;
        }

        .signature-field {
            flex: 1;
            text-align: center;
            margin: 0 10px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-bottom: 5px;
        }

        .signature-title {
            font-size: 12px;
        }

        #receipt-footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
        }

        .resort-name {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 16px;
            color: rgba(0, 0, 0, 0.1);
            text-transform: uppercase;
            letter-spacing: 2px;
            width: 100%;
            text-align: center;
            z-index: -1;
        }

        #buttons {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            padding: 8px 16px;
            margin: 0 5px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn-print {
            background-color: #4CAF50;
            color: white;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <img class="background-logo" src="{{ asset('image/lg.png') }}" alt="Watermark Logo">

        <div id="receipt-header">
            <h2>Transaction Receipt</h2>
            <p>Transaction Date: {{ $cost->cost_date->format('F j, Y') }}</p>
        </div>

        <table class="transaction-details">
            <tr>
                <th>Category</th>
                <td>{{ $cost->group->name }}</td>
            </tr>
            <tr>
                <th>Person/Shop</th>
                <td>{{ $cost->person->name }}</td>
            </tr>
            <tr>
                <th>Amount</th>
                <td>Rs. {{ number_format($cost->amount, 2) }}</td>
            </tr>
            <tr>
                <th>Added By</th>
                <td>{{ $cost->user?->name ?? 'System' }}</td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $cost->created_at->format('F j, Y g:i A') }}</td>
            </tr>
        </table>

        <div class="resort-name">
            
        </div>

        <div class="signature-row">
            <div class="signature-field">
                <div class="signature-line"></div>
                <div class="signature-title">Prepared</div>
                <div class="signature-title">By</div>
            </div>
            <div class="signature-field">
                <div class="signature-line"></div>
                <div class="signature-title">Receiver</div>
                <div class="signature-title"></div>
            </div>
            <div class="signature-field">
                <div class="signature-line"></div>
                <div class="signature-title">Approved</div>
                <div class="signature-title">By</div>
            </div>
        </div>

        <div id="receipt-footer">
            <p>Generated on {{ now()->format('F j, Y g:i A') }}</p>
        </div>

        <div id="buttons">
            <button class="btn btn-print" type="button" onclick="window.print(); return false;">
                Print
            </button>
            <a href="{{ route('costs.index') }}">
                <button class="btn btn-back">
                    Back
                </button>
            </a>
        </div>
    </div>
</body>
</html>