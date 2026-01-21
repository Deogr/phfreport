<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Tickets Bundle #{{ $ticket->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #f3f4f6;
            padding: 10px;
            font-size: 12px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 10px;
        }

        .ticket-card {
            background: white;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            page-break-inside: avoid;
        }

        .ticket-info h3 {
            margin: 0 0 2px 0;
            color: #333;
            font-size: 1rem;
        }

        .ticket-info p {
            margin: 1px 0;
            color: #666;
            font-size: 0.8em;
        }

        .ticket-code {
            text-align: right;
            border-left: 1px dashed #ccc;
            padding-left: 10px;
            min-width: 100px;
        }

        .code-display {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 0.9em;
            letter-spacing: 1px;
            background: #f8f9fa;
            padding: 3px 6px;
            border-radius: 3px;
            display: block;
            margin-bottom: 2px;
        }

        .action-no-print {
            text-align: center;
            margin-bottom: 10px;
            width: 100%;
        }

        .btn {
            background-color: #3b82f6;
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin: 0 5px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-print {
            background-color: #10b981;
        }

        h2 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container {
                max-width: 100%;
                display: block;
                /* Stack them in print if grid causes issues, or keep grid if it fits */
            }

            /* Force grid attempt on print for compaction, or fallback to small rows */
            .container {
                display: grid;
                grid-template-columns: 1fr 1fr;
                /* 2 columns on A4 portrait usually fits */
                gap: 10px;
            }

            .action-no-print {
                display: none;
            }

            .ticket-card {
                border: 1px solid #000;
                box-shadow: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="action-no-print">
        <button onclick="window.print()" class="btn btn-print">Print Tickets</button>
        <a href="{{ route('admin.tickets') }}" class="btn">Back to Sales</a>
    </div>

    <div class="container">
        <h2 style="text-align: center; margin-bottom: 20px;">Ticket Bundle #{{ $ticket->id }} -
            {{ $ticket->guest_name }}
        </h2>

        @foreach($ticket->items as $index => $item)
            <div class="ticket-card">
                <div style="margin-right: 15px;">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo" style="width: 60px;">
                </div>
                <div class="ticket-info">
                    <h3>PHF Ticket</h3>
                    <p><strong>Guest:</strong> {{ $ticket->guest_name }}</p>
                    <p><strong>Type:</strong> {{ ucfirst($ticket->type) }}</p>
                    <p><strong>Price:</strong> RWF {{ number_format($ticket->price_per_ticket) }}</p>
                    <p><strong>Date:</strong> {{ $ticket->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="ticket-code">
                    <span class="code-display">{{ $item->code }}</span>
                    <p>Ticket {{ $index + 1 }} of {{ $ticket->quantity }}</p>
                    <small>{{ $item->status }}</small>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>