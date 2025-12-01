<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Expenses - {{ Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}</title>
    <style>
        /* Print-specific styles */
        @media print {
            #buttons { display: none; }
            @page { margin: 0.5cm; }
        }

        /* General styles */
        body { 
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        #wrapper {
            max-width: 800px;
            margin: 0 auto;
        }

        #receipt-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .tb-expense-detail {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .tb-expense-detail th,
        .tb-expense-detail td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .tb-expense-total {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .tb-expense-total td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .category-header {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        #buttons {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            margin: 0 5px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }

        .btn-print {
            background-color: #4CAF50;
            color: white;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
        }

        #receipt-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <div id="receipt-header">
            <img width="200px" src="{{ asset('image/lg.png') }}" alt="Logo">
            
            <h2>Daily Expense Report</h2>
            <p>Date: <strong>{{ Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}</strong></p>
        </div>

        <table class="tb-expense-detail">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Person/Shop</th>
                    <th>Amount</th>
                    <th>Added By</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAmount = 0; @endphp
                @foreach($dailyGroupedCosts as $category => $persons)
                    <tr class="category-header">
                        <td colspan="4">{{ $category }}</td>
                    </tr>
                    @foreach($persons as $person => $data)
                        @foreach($data['costs'] as $cost)
                            <tr>
                                <td></td>
                                <td>{{ $person }}</td>
                                <td>Rs. {{ number_format($cost->amount, 2) }}</td>
                                <td>{{ $cost->user?->name ?? 'System' }}</td>
                            </tr>
                            @php $totalAmount += $cost->amount; @endphp
                        @endforeach
                        <tr class="total-row">
                            <td></td>
                            <td>Total for {{ $person }}</td>
                            <td colspan="2">Rs. {{ number_format($data['total'], 2) }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <table class="tb-expense-total">
            <tbody>
                <tr class="total-row">
                    <td width="50%">Total Expenses</td>
                    <td>Rs. {{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>

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