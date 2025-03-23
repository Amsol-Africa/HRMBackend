<!DOCTYPE html>
<html>

<head>
    <title>Bank Advice Report - {{ $payroll->payrun_year }}/{{ $payroll->payrun_month }}</title>
    <style>
    body {
        font-family: Arial, sans-serif;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .total {
        font-weight: bold;
    }
    </style>
</head>

<body>
    <div class="header">
        <h1>Bank Advice Report</h1>
        <p>Business: {{ $payroll->business->name ?? 'N/A' }}</p>
        <p>Period: {{ $payroll->payrun_year }} - {{ str_pad($payroll->payrun_month, 2, '0', STR_PAD_LEFT) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Bank Name</th>
                <th>Account Number</th>
                <th>Net Pay ({{ $payroll->currency }})</th>
            </tr>
        </thead>
        <tbody>
            @php $totalNetPay = 0; @endphp
            @forelse($payroll->employeePayrolls as $ep)
            <tr>
                <td>{{ $ep->employee->full_name ?? 'N/A' }}</td>
                <td>{{ $ep->employee->paymentDetails->bank_name ?? 'N/A' }}</td>
                <td>{{ $ep->employee->paymentDetails->account_number ?? 'N/A' }}</td>
                <td>{{ number_format($ep->net_pay, 2) }}</td>
                @php $totalNetPay += $ep->net_pay; @endphp
            </tr>
            @empty
            <tr>
                <td colspan="4">No data available</td>
            </tr>
            @endforelse
            <tr class="total">
                <td colspan="3">Total</td>
                <td>{{ number_format($totalNetPay, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>