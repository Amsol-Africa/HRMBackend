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
                @if($column === 'paye')
                <th>PIN of Employee</th>
                <th>Name of Employee</th>
                <th>Basic Salary ({{ $currency }})</th>
                <th>Gross Pay ({{ $currency }})</th>
                <th>PAYE Tax ({{ $currency }})</th>
                @elseif($column === 'shif')
                <th>PAYROLL NUMBER</th>
                <th>FIRSTNAME</th>
                <th>LASTNAME</th>
                <th>ID NO</th>
                <th>KRA PIN</th>
                <th>SHIF NO</th>
                <th>CONTRIBUTION AMOUNT ({{ $currency }})</th>
                <th>PHONE</th>
                @elseif($column === 'nssf')
                <th>PAYROLL NUMBER</th>
                <th>SURNAME</th>
                <th>OTHER NAMES</th>
                <th>ID NO</th>
                <th>KRA PIN</th>
                <th>NSSF NO</th>
                <th>GROSS PAY ({{ $currency }})</th>
                <th>VOLUNTARY</th>
                @elseif($column === 'housing_levy')
                <th>EMP NO</th>
                <th>FULL NAME</th>
                <th>TAX_NO</th>
                <th>HOUSE_LEVY AMOUNT ({{ $currency }})</th>
                @else
                <th>Name</th>
                <th>Code</th>
                <th>KRA PIN</th>
                <th>Basic Salary ({{ $currency }})</th>
                <th>Gross Pay ({{ $currency }})</th>
                <th>Net Pay ({{ $currency }})</th>
                <th>{{ ucwords(str_replace('_', ' ', $column)) }} ({{ $currency }})</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                @if($column === 'paye')
                <td>{{ $row['PIN of Employee'] }}</td>
                <td>{{ $row['Name of Employee'] }}</td>
                <td>{{ $row['Basic Salary'] }}</td>
                <td>{{ $row['Gross Pay (Ksh) (I)'] }}</td>
                <td>{{ $row['PAYE Tax (Ksh) (T)'] }}</td>
                @elseif($column === 'shif')
                <td>{{ $row['PAYROLL NUMBER'] }}</td>
                <td>{{ $row['FIRSTNAME'] }}</td>
                <td>{{ $row['LASTNAME'] }}</td>
                <td>{{ $row['ID NO'] }}</td>
                <td>{{ $row['KRA PIN'] }}</td>
                <td>{{ $row['SHIF NO'] }}</td>
                <td>{{ $row['CONTRIBUTION AMOUNT'] }}</td>
                <td>{{ $row['PHONE'] }}</td>
                @elseif($column === 'nssf')
                <td>{{ $row['PAYROLL NUMBER'] }}</td>
                <td>{{ $row['SURNAME'] }}</td>
                <td>{{ $row['OTHER NAMES'] }}</td>
                <td>{{ $row['ID NO'] }}</td>
                <td>{{ $row['KRA PIN'] }}</td>
                <td>{{ $row['NSSF NO'] }}</td>
                <td>{{ $row['GROSS PAY'] }}</td>
                <td>{{ $row['VOLUNTARY'] }}</td>
                @elseif($column === 'housing_levy')
                <td>{{ $row['EMP NO'] }}</td>
                <td>{{ $row['FULL NAME'] }}</td>
                <td>{{ $row['TAX_NO'] }}</td>
                <td>{{ $row['HOUSE_LEVY AMOUNT'] }}</td>
                @else
                <td>{{ $row['employee_name'] }}</td>
                <td>{{ $row['employee_code'] }}</td>
                <td>{{ $row['tax_no'] }}</td>
                <td>{{ $row['basic_salary'] }}</td>
                <td>{{ $row['gross_pay'] }}</td>
                <td>{{ $row['net_pay'] }}</td>
                <td>{{ $row[$column] }}</td>
                @endif
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