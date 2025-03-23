<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ ucwords(str_replace('_', ' ', $column)) }} Report - Payroll {{ $payroll->id }}</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 5mm;
            font-size: 12pt;
            color: #1a202c;
        }

        .header,
        .footer {
            width: 100%;
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #1a202c;
        }

        .header .left,
        .header .right {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .header .left {
            margin-right: 3%;
        }

        .header .right {
            text-align: right;
        }

        .header h1 {
            font-size: 20pt;
            margin: 0;
            font-weight: 700;
        }

        .header h2 {
            font-size: 16pt;
            margin: 0;
            font-weight: 600;
        }

        .text-muted {
            color: #6b7280;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #1a202c;
            padding: 10px;
            text-align: left;
        }

        .table th {
            background-color: #1a202c;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
        }

        .table td {
            font-size: 11pt;
        }

        .footer {
            margin-top: 20px;
            border-top: 2px solid #1a202c;
            padding-top: 10px;
            text-align: left;
        }

        .logo {
            max-height: 60px;
            max-width: 150px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        @page {
            margin: 10mm;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="left">
            @if($business->logo)
            <img src="{{ asset('storage/' . $business->logo) }}" alt="{{ $business->company_name }} Logo" class="logo">
            @else
            <div
                style="width: 60px; height: 60px; background-color: #e5e7eb; text-align: center; line-height: 60px; font-size: 24pt; font-weight: bold; color: #6b7280; margin-bottom: 10px;">
                {{ strtoupper(substr($business->company_name ?? 'Company', 0, 1)) }}
            </div>
            @endif
            <h1>{{ $business->company_name ?? 'Default Company Name' }}</h1>
            <p class="text-muted">{{ $business->physical_address ?? 'Default Address' }}</p>
            <p class="text-muted">Phone: {{ $business->phone ?? '+123-456-7890' }}</p>
            <p class="text-muted">Email: {{ $business->user->email ?? 'info@company.com' }}</p>
        </div>
        <div class="right">
            <h2>{{ ucwords(str_replace('_', ' ', $column)) }} Report</h2>
            <p class="text-muted">Payroll Period: {{ $payroll->payrun_month }}/{{ $payroll->payrun_year }}</p>
            <p class="text-muted">Payroll ID: {{ $payroll->id }}</p>
            <p class="text-muted">Currency: {{ $payroll->currency ?? 'KES' }}</p>
            <p class="text-muted">Date: {{ now()->format('F d, Y') }}</p>
        </div>
    </div>

    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Employee Code</th>
                <th>{{ ucwords(str_replace('_', ' ', $column)) }} ({{ $payroll->currency ?? 'KES' }})</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row['employee_name'] }}</td>
                <td>{{ $row['employee_code'] }}</td>
                <td>{{ $row[$column] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p class="text-muted">Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
        <p class="text-muted">For official use only.</p>
    </div>
</body>

</html>