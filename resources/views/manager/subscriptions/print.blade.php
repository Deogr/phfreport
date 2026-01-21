<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Report - PHF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .status-active {
            color: green;
            font-weight: bold;
        }

        .status-expired {
            color: #666;
        }

        .status-cancelled {
            color: red;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #333; color: #fff; border: none; cursor: pointer;">Print
            Report</button>
        <button onclick="window.history.back()"
            style="padding: 10px 20px; background: #ccc; border: none; cursor: pointer; margin-left: 10px;">Back</button>
    </div>

    <div class="header">
        <h1>PHF Report - Subscription List</h1>
        <p>Generated on: {{ now()->format('M d, Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Phone</th>
                <th>Service</th>
                <th>Dates</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscriptions as $sub)
                <tr>
                    <td>{{ $sub->user ? $sub->user->name : $sub->guest_name }}</td>
                    <td>{{ $sub->guest_phone ?? '-' }}</td>
                    <td>{{ $sub->service->name }}</td>
                    <td>{{ $sub->start_date->format('M d, Y') }} - {{ $sub->end_date->format('M d, Y') }}</td>
                    <td>{{ number_format($sub->price) }}</td>
                    <td class="status-{{ $sub->status }}">{{ ucfirst($sub->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>