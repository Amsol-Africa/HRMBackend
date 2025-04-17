<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $employeePayroll->employee->user->name ?? 'Employee' }}</title>
    <style>
    body {
        margin: 0;
        padding: 20px;
        background-color: #f4f4f4;
    }

    .payslip {
        width: 500px;
        margin: 0 auto;
        background-color: #fff;
        padding: 15px;
        border: 1px solid #ccc;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .header {
        text-align: center;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .header h1 {
        font-size: 18px;
        margin: 0;
    }

    .header h2 {
        font-size: 14px;
        margin: 5px 0 0;
        color: #555;
    }

    .section {
        margin-bottom: 15px;
    }

    .section h3 {
        font-size: 14px;
        font-weight: bold;
        margin: 0 0 5px 0;
        border-bottom: 1px solid #ddd;
        padding-bottom: 2px;
    }

    .details p,
    .summary p {
        font-size: 12px;
        margin: 3px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    th,
    td {
        padding: 5px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #f5f5f5;
        font-weight: bold;
    }

    .total {
        font-weight: bold;
        background-color: #f9f9f9;
    }

    .footer {
        text-align: center;
        font-size: 10px;
        color: #777;
        margin-top: 15px;
        border-top: 1px solid #ddd;
        padding-top: 10px;
    }
    </style>
</head>

<body>
    <div class="payslip">
        <div class="header">
            @if($entityType === 'business' && $entity->logo)
            <img src="{{ config('app.url') }}/media/amsol-logo.png" alt="{{ config('app.name') }} Logo"
                style="max-height: 50px; max-width: 100px; margin-bottom: 5px;">
            @elseif($entityType === 'location' && $business->logo)
            <img src="{{ config('app.url') }}/media/amsol-logo.png" alt="{{ config('app.name') }} Logo"
                style="max-height: 50px; max-width: 100px; margin-bottom: 5px;">
            @endif
            <h1>{{ $entity->company_name ?? $entity->name ?? 'Business Name' }}</h1>
            <p>{{ $entity->physical_address ?? 'N/A' }}</p>
            <p>Email:
                {{ ($entityType === 'business' && $entity->user) ? $entity->user->email : ($business->user->email ?? 'N/A') }}
            </p>
            <h2>Payslip for
                {{ \Carbon\Carbon::create($employeePayroll->payroll->payrun_year, $employeePayroll->payroll->payrun_month)->format('F Y') }}
            </h2>
        </div>

        <!-- Employee Details -->
        <div class="section details">
            <h3>Employee Details</h3>
            <p><strong>Name:</strong> {{ $employeePayroll->employee->user->name ?? 'N/A' }}</p>
            <p><strong>Employee Code:</strong> {{ $employeePayroll->employee->employee_code ?? 'N/A' }}</p>
            <p><strong>Tax No:</strong> {{ $employeePayroll->employee->tax_no ?? 'N/A' }}</p>
            @if($entityType === 'location')
            <p><strong>Location:</strong> {{ $entity->name ?? 'N/A' }}</p>
            @endif
        </div>

        <!-- Earnings -->
        <div class="section">
            <h3>Earnings</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount ({{ $employeePayroll->payroll->currency ?? 'KES' }})</th>
                        <th>Amount ({{ $targetCurrency ?? 'USD' }})</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Salary</td>
                        <td>{{ number_format($employeePayroll->basic_salary ?? 0, 2) }}</td>
                        <td>{{ number_format(($employeePayroll->basic_salary ?? 0) * ($exchangeRates ?? 1), 2) }}</td>
                    </tr>
                    <tr>
                        <td>Overtime</td>
                        <td>
                            <?php
                            $overtimeData = json_decode($employeePayroll->overtime, true);
                            $overtimeAmount = is_array($overtimeData) && isset($overtimeData['amount']) ? $overtimeData['amount'] : 0;
                            ?>
                            {{ number_format($overtimeAmount, 2) }}
                        </td>
                        <td>{{ number_format($overtimeAmount * ($exchangeRates ?? 1), 2) }}</td>
                    </tr>
                    <?php $allowances = json_decode($employeePayroll->allowances, true) ?? []; ?>
                    @foreach($allowances as $allowance)
                    <tr>
                        <td>{{ $allowance['name'] ?? 'Allowance' }}</td>
                        <td>{{ number_format($allowance['amount'] ?? 0, 2) }}</td>
                        <td>{{ number_format(($allowance['amount'] ?? 0) * ($exchangeRates ?? 1), 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total">
                        <td>Gross Pay</td>
                        <td>{{ number_format($employeePayroll->gross_pay ?? 0, 2) }}</td>
                        <td>{{ number_format(($employeePayroll->gross_pay ?? 0) * ($exchangeRates ?? 1), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Reliefs -->
        <div class="section">
            <h3>Reliefs</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount ({{ $employeePayroll->payroll->currency ?? 'KES' }})</th>
                        <th>Amount ({{ $targetCurrency ?? 'USD' }})</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $reliefs = json_decode($employeePayroll->reliefs, true) ?? []; ?>
                    @foreach($reliefs as $reliefKey => $reliefData)
                    @if(is_array($reliefData) && isset($reliefData['amount']))
                    <tr>
                        <td>{{ $reliefData['name'] ?? ucwords(str_replace('-', ' ', $reliefKey)) }}</td>
                        <td>{{ number_format($reliefData['amount'], 2) }}</td>
                        <td>{{ number_format($reliefData['amount'] * ($exchangeRates ?? 1), 2) }}</td>
                    </tr>
                    @endif
                    @endforeach
                    @if(empty($reliefs))
                    <tr>
                        <td colspan="3">No reliefs applied</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Deductions -->
        <div class="section">
            <h3>Deductions</h3>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount ({{ $employeePayroll->payroll->currency ?? 'KES' }})</th>
                        <th>Amount ({{ $targetCurrency ?? 'USD' }})</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([
                    'shif' => 'SHIF',
                    'nssf' => 'NSSF',
                    'paye' => 'PAYE',
                    'housing_levy' => 'Housing Levy',
                    'helb' => 'HELB',
                    'loan_repayment' => 'Loans',
                    'advance_recovery' => 'Advances'
                    ] as $key => $label)
                    @if(isset($employeePayroll->$key) && $employeePayroll->$key > 0)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ number_format($employeePayroll->$key, 2) }}</td>
                        <td>{{ number_format($employeePayroll->$key * ($exchangeRates ?? 1), 2) }}</td>
                    </tr>
                    @endif
                    @endforeach
                    <?php $deductions = json_decode($employeePayroll->deductions, true) ?? []; ?>
                    @foreach($deductions as $deduction)
                    @if(is_array($deduction) && isset($deduction['amount']))
                    <tr>
                        <td>{{ $deduction['name'] }}</td>
                        <td>{{ number_format($deduction['amount'], 2) }}</td>
                        <td>{{ number_format($deduction['amount'] * ($exchangeRates ?? 1), 2) }}</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Net Pay -->
        <div class="section summary">
            <h3>Net Pay</h3>
            <p><strong>Net Pay ({{ $employeePayroll->payroll->currency ?? 'KES' }}):</strong>
                {{ number_format($employeePayroll->net_pay ?? 0, 2) }}
            </p>
            <p><strong>Net Pay ({{ $targetCurrency ?? 'USD' }}):</strong>
                {{ number_format(($employeePayroll->net_pay ?? 0) * ($exchangeRates ?? 1), 2) }}
            </p>
        </div>

        <!-- Bank Details -->
        <div class="section">
            <h3>Bank Details</h3>
            <p><strong>Bank Name:</strong> {{ $employeePayroll->bank_name ?? 'N/A' }}</p>
            <p><strong>Account Number:</strong> {{ $employeePayroll->account_number ?? 'N/A' }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on: {{ now()->format('F d, Y') }}</p>
            <p>For official use only</p>
        </div>
    </div>
</body>

</html>