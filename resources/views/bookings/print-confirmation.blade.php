<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Confirmation</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .header {
            position: relative;
            margin-bottom: 30px;
        }
        .header-content {
            float: left;
            width: 70%;
        }

        .value-highlight {
    font-weight: bold;
    color: #000;
}

.header-content div {
    margin-bottom: 8px;
    font-size: 13pt;
}

.payment-details {
    margin-left: 20px;
    margin-bottom: 10px;
    line-height: 1.8;
}

.signature-section {
    margin-top: 60px;
    border-top: 1px solid #eee;
    padding-top: 20px;
}

.signature-line {
    margin: 10px 0;
    border-bottom: 1px solid #000;
    min-width: 200px;
    display: inline-block;
}

.signature-column p {
    margin: 15px 0;
}

.terms-list {
    text-align: justify;
}

.terms-list li {
    margin-bottom: 15px;
}
        .logo {
            float: right;
            width: 80px;
            height: 80px;
        }
        .booking-details {
            clear: both;
            margin-bottom: 20px;
        }
        .booking-confirmation {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        .terms-list {
            padding-left: 20px;
        }
        .terms-list li {
            margin-bottom: 10px;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .signature-column {
            width: 45%;
        }
        .signature-line {
            margin: 10px 0;
            border-bottom: 1px dotted #000;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .action-buttons {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-size: 15px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .btn-print {
        background: linear-gradient(45deg, #00c853, #64dd17);
        color: white;
    }

    .btn-print:hover {
        background: linear-gradient(45deg, #00e676, #76ff03);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }

    .btn-back {
        background: linear-gradient(45deg, #2196f3, #00bcd4);
        color: white;
    }

    .btn-back:hover {
        background: linear-gradient(45deg, #42a5f5, #26c6da);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }

    /* Add icon styles */
    .btn i {
        font-size: 18px;
    }

    /* Add shine effect */
    .btn::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        bottom: -50%;
        left: -50%;
        background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,0.13) 47%, rgba(255,255,255,0.13) 100%);
        transform: rotate(45deg);
        transition: 0.8s;
    }

    .btn:hover::after {
        transform: rotate(45deg) translate(-100%, -100%);
    }

    /* Pulse animation */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    .btn:active {
        transform: scale(0.95);
    }

    @media print {
        .action-buttons {
            display: none;
        }
    }

    .reservation-number {
        font-weight: bold;
        font-size: 1.1em;
        margin-bottom: 8px;
    }
    .money {
    font-family: "Arial", sans-serif;
    font-weight: bold;
    white-space: nowrap;
}
.terms-list > li {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.terms-list > li:last-child {
    border-bottom: none;
}

.contact-info {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.contact-info div {
    margin-bottom: 10px;
    color: #555;
}


    </style>
<!-- Add this CSS link in the head section for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    
</head>
<body>





    <div class="header clearfix">
    <div class="header-content">
    <div class="reservation-number">Reservation Number: <span class="value-highlight">{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</span></div>
    <div>Package/Rate: <span class="value-highlight">{{ $booking->name }}</span></div>
    <div>Event Date: <span class="value-highlight">{{ $booking->start ? $booking->start->format('Y-m-d') : 'N/A' }}</span></div>
    <div>Function Type: <span class="value-highlight">{{ $booking->function_type }}</span></div>
    @if($isWedding)
    <div>Venue: <span class="value-highlight">Grand Ballroom</span></div>
    @endif
</div>
        <img src="{{ asset('image/Holiday.png') }}" class="logo" alt="Logo">
    </div>

    <div class="booking-confirmation">Booking Confirmation</div>

    @if($isWedding)
        <ol class="terms-list">
        <li>
    <strong>Advance Payment Details:</strong>
    @if($booking->payments->count() > 0)
        @foreach($booking->payments as $index => $payment)
            @php
                $ordinal = match($index + 1) {
                    1 => '1st',
                    2 => '2nd',
                    3 => '3rd',
                    default => ($index + 1) . 'th'
                };
            @endphp
            <div class="payment-details">
                <strong>{{ $ordinal }} Payment:</strong><br>
                Amount: <span class="value-highlight">Rs. {{ number_format($payment->amount, 2) }}</span><br>
                Date: <span class="value-highlight">{{ $payment->payment_date ? $payment->payment_date->format('Y-m-d') : 'N/A' }}</span><br>
                Bill Number: <span class="value-highlight">{{ $payment->bill_number ?? 'N/A' }}</span>
                @if($index === 0)
                    <br><em>(Advance Payment is Non-Refundable and Non-Transferable)</em>
                @endif
            </div>
        @endforeach
    @else
        <div class="payment-details">No payment records found.</div>
    @endif
</li>
            
            
            
<li><strong>Package Details:</strong> <span class="value-highlight">{{ $booking->name }}</span></li>
<li><strong>Guest Count:</strong> <span class="value-highlight">{{ $booking->guest_count }}</span></li>
           
            <li><strong>Payments --</strong> All the Payment should be done 30 Days before the function. The absolve amount must be paid on the event date.</li>
            <li><strong>Guest Count Confirmation:</strong> The final guest count must be confirmed at least two (2) weeks prior to the function date. Otherwise, charges will be based on the initial guest count.</li>
            
            <li><strong>Child Policy -</strong> No child policy and the count will be taken based on plates & not the heads.</li>
            <li><strong>Function Duration:</strong> The Banquet Hall is provided for a maximum of seven (7) hours. Additional time will be charged at Rs. 25,000/- per hour.</li>

            
            <li><strong>Bites -</strong> Dry Bites & Mixtures can bring from outside, cooked bites needed to buy from the hotel</li>
            <li><strong>Soft Drinks-</strong> All soft drinks must be purchased from the hotel.</li>
            <li><strong>Music -</strong> Must be closed 11.00 Pm in the night function.</li>
            <li><strong>Foods -</strong>Buffet service is available for a maximum of Two and a half hours. Foods are not allowed to carry outside from the Hotel</li>
            <li><strong>Liquor Service Bar -</strong> Must be close before two hours from end of the function.</li>
            <li><strong>Damages -</strong> The patron responsible and agrees to indemnify to the Soba Lanka Resort for all Damages Sustained to the hotel and Grounds during an event by any of Employees/Contractor invitees/Guest of the organizer.</li>
            <li><strong>Service Charge -</strong> A service charge of at least Rs 5000 shall be added to the total amount paid</li>
        </ol>

        <div>I hereby fully accept and understand all 14 clauses of the above-mentioned contract. I am willing to follow all of the Hotel's instructions and terms. This includes all of the charges mentioned above. I am confident in signing this contract.</div>

        <div class="signature-section" style="margin-top: 60px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; padding-right: 20px;">
                <p style="margin-bottom: 15px;">On behalf of the Patron -- Bride / Groom</p>
                <p>Date: <span style="display: inline-block; width: 200px; border-bottom: 1px solid black;"></span></p>
                <p>National ID: <span style="display: inline-block; width: 200px; border-bottom: 1px solid black;"></span></p>
                <p>Name: <span style="display: inline-block; width: 200px; border-bottom: 1px solid black;"></span></p>
                <p>Signature: <span style="display: inline-block; width: 200px; border-bottom: 1px solid black;"></span></p>
            </td>
            <td style="width: 50%; padding-left: 20px;">
                <p style="margin-bottom: 15px;">On behalf of Soba Lanka Holiday Resort</p>
                <p>Date: <span style="display: inline-block; width: 200px; border-bottom: 1px solid black;"></span></p>
                <p>Name: <span style="display: inline-block; width: 200px; border-bottom: 1px solid black;"></span></p>
                <p>Designation: <span style="display: inline-block; width: 200px; border-bottom: 1px solid black;"></span></p>
                <p>Signature: <span style="display: inline-block; width: 200px; border-bottom: 1px solid black;"></span></p>
            </td>
        </tr>
    </table>
</div>
    @else
        <ol class="terms-list">
        <li>
        <strong>Advance Payment Details:</strong>
        @if($booking->payments->count() > 0)
            @foreach($booking->payments as $index => $payment)
                @php
                    $ordinal = match($index + 1) {
                        1 => '1st',
                        2 => '2nd',
                        3 => '3rd',
                        default => ($index + 1) . 'th'
                    };
                @endphp
                <div class="payment-details">
                    <strong>{{ $ordinal }} Payment:</strong><br>
                    Amount: <span class="value-highlight">Rs. {{ number_format($payment->amount, 2) }}</span><br>
                    Date: <span class="value-highlight">{{ $payment->payment_date ? $payment->payment_date->format('Y-m-d') : 'N/A' }}</span><br>
                    Bill Number: <span class="value-highlight">{{ $payment->bill_number ?? 'N/A' }}</span>
                    @if($index === 0)
                        <br><em>(Advance Payment is Non-Refundable and Non-Transferable)</em>
                    @endif
                </div>
            @endforeach
        @else
            <div class="payment-details">No payment records found.</div>
        @endif
    </li>
            
            
            
    <li><strong>Package Details:</strong> <span class="value-highlight">{{ $booking->name }}</span></li>
            
            
            
            <li><strong>Guest Count:</strong> <span class="value-highlight">{{ $booking->guest_count }}</span>
        <div style="margin-top: 5px;"><em>Note: Group Night-Stay Packages & Group Dayout Packages require a minimum of ten guests.</em></div>
    </li>
            
            <li><strong>Child Policy -</strong> Children under 5 years stay free of charge. Children aged 6-12 years receive a 50% discount on regular packages.</li>
            <li><strong>Function Duration:</strong> Each package has specific time allocations:
    <ul style="list-style-type: none; margin-left: 20px;">
        <li>• Day Out: <span class="value-highlight">09:00 AM</span> to <span class="value-highlight">04:30 PM</span></li>
        <li>• Half Board: <span class="value-highlight">03:00 PM</span> to <span class="value-highlight">10:00 AM</span> (next day)</li>
        <li>• Full Board: <span class="value-highlight">03:00 PM</span> to <span class="value-highlight">03:00 PM</span> (next day)</li>
    </ul>
    <div style="margin-top: 5px;"><em>Note: Exceeding these time frames will incur an additional charge of <span class="value-highlight">Rs. 5,000/-</span> per hour.</em></div>
</li>
            
            <li><strong>Bites -</strong>Cooked bites needed to buy from the hotel.</li>
            <li><strong>Soft Drinks-</strong> All soft drinks must be purchased from the hotel.</li>
            <li><strong>Foods -</strong>Buffet service is available for a maximum of Two hours.</li>
            <li><strong>Damages -</strong> The patron responsible and agrees to indemnify to the Soba Lanka Resort for all Damages Sustained to the hotel and Grounds during an event by any of Employees/Contractor invitees/Guest of the organizer.</li>
            <li><strong>Service Charge:</strong> A service charge of at least <span class="value-highlight money">Rs. 5,000/-</span> shall be added to the total amount paid.</li>
        </ol>

        <div style="margin-top: 20px;">*This document is computer-generated. If you have any questions, please contact us.*</div>
        <div class="contact-info">
    
    <div><strong>Address:</strong> Soba Lanka Holiday Resort, Balawattala Road, Melsiripura, Kurunegala</div>
    <div><strong>Phone:</strong> 037 2250308</div>
    <div><strong>Email:</strong> sobalankahotel@gmail.com</div>
    <div><strong>Website:</strong> sobalanka.com</div>
</div>
        @endif

    @if(isset($showPrintButton) && $showPrintButton)
    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>
    @endif
</body>
<!-- Add this just after the <body> tag -->
<div class="action-buttons">
    <button onclick="window.print()" class="btn btn-print">
        <i class="fas fa-print"></i>
        Print
    </button>
    <a href="{{ route('calendar') }}" class="btn btn-back">
        <i class="fas fa-calendar-alt"></i>
        Back to Calendar
    </a>
</div>

</html>