<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Company Payslip - {{ $payroll->payrun_year }}/{{ str_pad($payroll->payrun_month, 2, '0', STR_PAD_LEFT) }}
    </title>
    <style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        margin: 5mm;
        font-size: 10pt;
        color: #1a202c;
    }

    .header,
    .footer {
        width: 100%;
        padding-bottom: 10px;
        margin-bottom: 15px;
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
        font-size: 18pt;
        margin: 0;
        font-weight: 700;
    }

    .header h2 {
        font-size: 14pt;
        margin: 0;
        font-weight: 600;
    }

    .text-muted {
        color: #6b7280;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        page-break-inside: avoid;
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
    .table td:first-child {
        text-align: left;
    }

    .table tfoot td {
        background-color: #f9fafb;
        font-weight: 600;
    }

    .section-title {
        font-size: 12pt;
        font-weight: 600;
        margin: 10px 0;
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
        margin: 5mm;
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
                {{ ($entityType === 'business' ? $entity->phone : $business->phone) ?? '+123-456-7890' }}</p>
            <p class="text-muted">Email:
                {{ ($entityType === 'business' && $entity->user ? $entity->user->email : $business->user->email) ?? 'info@company.com' }}
            </p>
        </div>
        <div class="right">
            <h2>Payroll Report</h2>
            <p class="text-muted">Period: {{ $payroll->payrun_year }} -
                {{ str_pad($payroll->payrun_month, 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-muted">Payroll ID: {{ $payroll->id }}</p>
            <p class="text-muted">Currency: {{ $currency ?? 'KES' }}</p>
            <p class="text-muted">Date: {{ now()->format('F d, Y') }}</p>
        </div>
    </div>

    <!-- Employee Details Table -->
    <div class="section-title">Employee Details</div>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Tax No</th>
                <th>Bank Name</th>
                <th>Account Number</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $row['employee_name'] }}</td>
                <td>{{ $row['employee_code'] }}</td>
                <td>{{ $row['tax_no'] }}</td>
                <td>{{ $row['bank_name'] }}</td>
                <td>{{ $row['account_number'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5">No data available</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Earnings and Tax Table -->
    <div class="section-title">Earnings and Tax</div>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Basic Salary ({{ $currency }})</th>
                <th>Gross Pay</th>
                <th>Overtime</th>
                <th>Taxable Income</th>
                <th>PAYE</th>
                <th>PAYE Before Reliefs</th>
                <th>Personal Relief</th>
                <th>Insurance Relief</th>
                <th>Pay After Tax</th>
                <th>Net Pay</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $row['employee_name'] }}</td>
                <td>{{ number_format($row['basic_salary'], 2) }}</td>
                <td>{{ number_format($row['gross_pay'], 2) }}</td>
                <td>{{ number_format($row['overtime'], 2) }}</td>
                <td>{{ number_format($row['taxable_income'], 2) }}</td>
                <td>{{ number_format($row['paye'], 2) }}</td>
                <td>{{ number_format($row['paye_before_reliefs'], 2) }}</td>
                <td>{{ number_format($row['personal_relief'], 2) }}</td>
                <td>{{ number_format($row['insurance_relief'], 2) }}</td>
                <td>{{ number_format($row['pay_after_tax'], 2) }}</td>
                <td>{{ number_format($row['net_pay'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11">No data available</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>Totals</td>
                <td>{{ number_format($totals['totalBasicSalary'], 2) }}</td>
                <td>{{ number_format($totals['totalGrossPay'], 2) }}</td>
                <td>{{ number_format($totals['totalOvertime'], 2) }}</td>
                <td>{{ number_format($totals['totalTaxableIncome'], 2) }}</td>
                <td>{{ number_format($totals['totalPaye'], 2) }}</td>
                <td>{{ number_format($totals['totalPayeBeforeReliefs'], 2) }}</td>
                <td>{{ number_format($totals['totalPersonalRelief'], 2) }}</td>
                <td>{{ number_format($totals['totalInsuranceRelief'], 2) }}</td>
                <td>{{ number_format($totals['totalPayAfterTax'], 2) }}</td>
                <td>{{ number_format($totals['totalNetPay'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Deductions and Attendance Table -->
    <div class="section-title">Deductions and Attendance</div>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>SHIF</th>
                <th>NSSF</th>
                <th>Housing Levy</th>
                <th>HELB</th>
                <th>Loan Repayment</th>
                <th>Advance Recovery</th>
                <th>Custom Deductions</th>
                <th>Deductions After Tax</th>
                <th>Days Present</th>
                <th>Days Absent</th>
                <th>Days in Month</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $row['employee_name'] }}</td>
                <td>{{ number_format($row['shif'], 2) }}</td>
                <td>{{ number_format($row['nssf'], 2) }}</td>
                <td>{{ number_format($row['housing_levy'], 2) }}</td>
                <td>{{ number_format($row['helb'], 2) }}</td>
                <td>{{ number_format($row['loan_repayment'], 2) }}</td>
                <td>{{ number_format($row['advance_recovery'], 2) }}</td>
                <td>{{ number_format($row['custom_deductions'], 2) }}</td>
                <td>{{ number_format($row['deductions_after_tax'], 2) }}</td>
                <td>{{ $row['attendance_present'] }}</td>
                <td>{{ $row['attendance_absent'] }}</td>
                <td>{{ $row['days_in_month'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="12">No data available</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>Totals</td>
                <td>{{ number_format($totals['totalShif'], 2) }}</td>
                <td>{{ number_format($totals['totalNssf'], 2) }}</td>
                <td>{{ number_format($totals['totalHousingLevy'], 2) }}</td>
                <td>{{ number_format($totals['totalHelb'], 2) }}</td>
                <td>{{ number_format($totals['totalLoans'], 2) }}</td>
                <td>{{ number_format($totals['totalAdvances'], 2) }}</td>
                <td>{{ number_format($totals['totalCustomDeductions'], 2) }}</td>
                <td>{{ number_format($totals['totalDeductionsAfterTax'], 2) }}</td>
                <td>{{ $totals['totalAttendancePresent'] }}</td>
                <td>{{ $totals['totalAttendanceAbsent'] }}</td>
                <td>{{ $totals['totalDaysInMonth'] }}</td>
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