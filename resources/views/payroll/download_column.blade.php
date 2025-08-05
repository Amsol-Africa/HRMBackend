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
            @php
            $logoUrl = $business->getImageUrl();
            $logoBase64 = null;

            $filePath = public_path(parse_url($logoUrl, PHP_URL_PATH));

            if (is_file($filePath)) {
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($filePath));
            }
            @endphp

            @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="{{ $business->company_name }} Logo"
                style="max-height:60px; max-width:150px; object-fit:contain;">
            @else
            <div class="logo-placeholder">{{ strtoupper(substr($business->company_name ?? 'Company', 0, 1)) }}</div>
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
                <td>{{ $row[0] }}</td> <!-- PIN of Employee -->
                <td>{{ $row[1] }}</td> <!-- Name of Employee -->
                <td>{{ $row[4] }}</td> <!-- Basic Salary -->
                <td>{{ $row[11] }}</td> <!-- Other Allowance (as proxy for Gross Pay) -->
                <td>{{ $row[34] }}</td> <!-- Self Assessed PAYE -->
                @elseif($column === 'shif')
                <td>{{ $row[0] }}</td> <!-- PAYROLL NUMBER -->
                <td>{{ $row[1] }}</td> <!-- FIRSTNAME -->
                <td>{{ $row[2] }}</td> <!-- LASTNAME -->
                <td>{{ $row[3] }}</td> <!-- ID NO -->
                <td>{{ $row[4] }}</td> <!-- KRA PIN -->
                <td>{{ $row[5] }}</td> <!-- SHIF NO -->
                <td>{{ $row[6] }}</td> <!-- CONTRIBUTION AMOUNT -->
                <td>{{ $row[7] }}</td> <!-- PHONE -->
                @elseif($column === 'nssf')
                <td>{{ $row[0] }}</td> <!-- PAYROLL NUMBER -->
                <td>{{ $row[1] }}</td> <!-- SURNAME -->
                <td>{{ $row[2] }}</td> <!-- OTHER NAMES -->
                <td>{{ $row[3] }}</td> <!-- ID NO -->
                <td>{{ $row[4] }}</td> <!-- KRA PIN -->
                <td>{{ $row[5] }}</td> <!-- NSSF NO -->
                <td>{{ $row[6] }}</td> <!-- GROSS PAY -->
                <td>{{ $row[7] }}</td> <!-- VOLUNTARY -->
                @elseif($column === 'housing_levy')
                <td>{{ $row[0] }}</td> <!-- EMP NO -->
                <td>{{ $row[1] }}</td> <!-- FULL NAME -->
                <td>{{ $row[2] }}</td> <!-- TAX_NO -->
                <td>{{ $row[3] }}</td> <!-- HOUSE_LEVY AMOUNT -->
                @else
                <td>{{ $row[0] }}</td> <!-- employee_name -->
                <td>{{ $row[1] }}</td> <!-- employee_code -->
                <td>{{ $row[2] }}</td> <!-- tax_no -->
                <td>{{ $row[3] }}</td> <!-- basic_salary -->
                <td>{{ $row[4] }}</td> <!-- gross_pay -->
                <td>{{ $row[5] }}</td> <!-- net_pay -->
                @if($column === 'basic_salary')
                <td>{{ $row[3] }}</td> <!-- basic_salary -->
                @elseif($column === 'gross_pay')
                <td>{{ $row[4] }}</td> <!-- gross_pay -->
                @elseif($column === 'net_pay')
                <td>{{ $row[5] }}</td> <!-- net_pay -->
                @elseif($column === 'tax_no')
                <td>{{ $row[2] }}</td> <!-- tax_no -->
                @else
                <td>{{ $row[6] ?? 'N/A' }}</td> <!-- other columns -->
                @endif
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