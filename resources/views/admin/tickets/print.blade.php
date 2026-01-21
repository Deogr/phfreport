<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Receipt #{{ $ticket->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f3f4f6;
            padding: 10px;
            font-size: 12px;
        }

        .receipt {
            max-width: 280px;
            margin: 0 auto;
            background: white;
            padding: 10px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 1.2rem;
        }

        .info {
            margin-bottom: 10px;
        }

        .info p {
            margin: 2px 0;
            display: flex;
            justify-content: space-between;
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }

        .total {
            font-weight: bold;
            font-size: 1rem;
            text-align: right;
            margin: 5px 0;
        }

        .footer {
            text-align: center;
            font-size: 0.7rem;
            margin-top: 10px;
        }

        .action-no-print {
            margin-top: 10px;
            text-align: center;
        }

        .btn {
            background-color: #3b82f6;
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            font-size: 12px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt {
                box-shadow: none;
                width: 100%;
                max-width: none;
                padding: 0;
            }

            .action-no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="receipt">
        <div class="header">
            <img src="{{ asset('logo.jpg') }}" alt="PHF Logo" style="max-width: 80px; margin-bottom: 10px;">
            <h1>PHF TICKET</h1>
        </div>

        <div class="divider"></div>

        <div class="info">
            <p><span>Receipt No:</span> <span>#{{ $ticket->id }}</span></p>
            <p><span>Date:</span> <span>{{ $ticket->created_at->format('d/m/Y H:i') }}</span></p>
            <p><span>Guest:</span> <span>{{ $ticket->guest_name }}</span></p>
        </div>

        <div class="divider"></div>

        <div class="info">
            <p><span>Type:</span> <span>{{ ucfirst($ticket->type) }}</span></p>
            <p><span>Quantity:</span> <span>{{ $ticket->quantity }}</span></p>
            <p><span>Price/Tick:</span> <span>{{ number_format($ticket->price_per_ticket) }}</span></p>
        </div>

        <div class="divider"></div>

        <p class="total">Total: RWF {{ number_format($ticket->total_amount) }}</p>
        <p style="text-align: right; font-size: 0.9em;">Paid via {{ ucfirst($ticket->payment_method) }}</p>

        <div class="divider"></div>

        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>Please keep this receipt.</p>
        </div>
    </div>

    <div class="action-no-print">
        <a href="{{ route('admin.tickets') }}" class="btn">Back to Sales</a>
    </div>
</body>

</html>