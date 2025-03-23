<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Company Payslip - {{ $payroll->payrun_year }}/{{ str_pad($payroll->payrun_month, 2, '0', STR_PAD_LEFT) }}
    </title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 2mm;
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
            margin-top: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #1a202c;
            padding: 5px;
            text-align: right;
        }

        .table th {
            background-color: #1a202c;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9pt;
        }

        .table td {
            font-size: 9pt;
        }

        .table th:first-child,
        .table td:first-child,
        .table th:nth-child(2),
        .table td:nth-child(2) {
            text-align: left;
        }

        .table tfoot td {
            background-color: #f9fafb;
            font-weight: 600;
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
            margin: 2mm;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="left">
            @if($entityType === 'business' && $entity->logo)
            <img src="{{ asset('storage/' . $entity->logo) }}" alt="{{ $entity->company_name }} Logo" class="logo">
            @elseif($entityType === 'location' && $business->logo)
            <img src="{{ asset('storage/' . $business->logo) }}" alt="{{ $business->company_name }} Logo" class="logo">
            @else
            <div
                style="width: 60px; height: 60px; background-color: #e5e7eb; text-align: center; line-height: 60px; font-size: 24pt; font-weight: bold; color: #6b7280; margin-bottom: 10px;">
                {{ strtoupper(substr($entity->company_name ?? $entity->name ?? 'Company', 0, 1)) }}
            </div>
            @endif
            <h1>{{ $entity->company_name ?? $entity->name ?? 'Default Company Name' }}</h1>
            <p class="text-muted">{{ $entity->physical_address ?? 'Default Address' }}</p>
            <p class="text-muted">Phone:
                {{ ($entityType === 'business' ? $entity->phone : $business->phone) ?? '+123-456-7890' }}
            </p>
            <p class="text-muted">Email:
                {{ ($entityType === 'business' && $entity->user ? $entity->user->email : $business->user->email) ?? 'info@company.com' }}
            </p>
        </div>
        <div class="right">
            <h2>Payroll Report</h2>
            <p class="text-muted">Period: {{ $payroll->payrun_year }} -
                {{ str_pad($payroll->payrun_month, 2, '0', STR_PAD_LEFT) }}
            </p>
            <p class="text-muted">Payroll ID: {{ $payroll->id }}</p>
            <p class="text-muted">Currency: {{ $payroll->currency ?? 'KES' }}</p>
            <p class="text-muted">Date: {{ now()->format('F d, Y') }}</p>
        </div>
    </div>

    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Basic Salary ({{ $payroll->currency ?? 'KES' }})</th>
                <th>Gross Pay</th>
                <th>Overtime</th>
                <th>SHIF</th>
                <th>NSSF</th>
                <th>PAYE</th>
                <th>NHDF</th>
                <th>HELB</th>
                <th>Loans</th>
                <th>Advances</th>
                <th>Net Pay</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $row['employee_name'] }}</td>
                <td>{{ $row['employee_code'] }}</td>
                <td>{{ $row['basic_salary'] }}</td>
                <td>{{ $row['gross_pay'] }}</td>
                <td>{{ $row['overtime'] }}</td>
                <td>{{ $row['shif'] }}</td>
                <td>{{ $row['nssf'] }}</td>
                <td>{{ $row['paye'] }}</td>
                <td>{{ $row['housing_levy'] }}</td>
                <td>{{ $row['helb'] }}</td>
                <td>{{ $row['loans'] }}</td>
                <td>{{ $row['advances'] }}</td>
                <td>{{ $row['net_pay'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="13">No data available</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Totals</td>
                <td>{{ number_format($totals['totalBasicSalary'], 2) }}</td>
                <td>{{ number_format($totals['totalGrossPay'], 2) }}</td>
                <td>{{ number_format($totals['totalOvertime'], 2) }}</td>
                <td>{{ number_format($totals['totalShif'], 2) }}</td>
                <td>{{ number_format($totals['totalNssf'], 2) }}</td>
                <td>{{ number_format($totals['totalPaye'], 2) }}</td>
                <td>{{ number_format($totals['totalHousingLevy'], 2) }}</td>
                <td>{{ number_format($totals['totalHelb'], 2) }}</td>
                <td>{{ number_format($totals['totalLoans'], 2) }}</td>
                <td>{{ number_format($totals['totalAdvances'], 2) }}</td>
                <td>{{ number_format($totals['totalNetPay'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p class="text-muted">Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
        <p class="text-muted">For official use only.</p>
    </div>
</body>

</html>