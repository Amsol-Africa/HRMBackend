<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $employeePayroll->employee->user->name ?? 'Employee' }}</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 20px;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .header img {
        width: 100px;
        height: auto;
    }

    .header h1 {
        font-size: 16px;
        margin: 5px 0;
        text-transform: uppercase;
    }

    .header h2 {
        font-size: 14px;
        margin: 5px 0;
    }

    .business-details {
        text-align: center;
        font-size: 11px;
        margin-bottom: 10px;
    }

    .employee-details {
        margin-bottom: 20px;
        overflow: hidden;
    }

    .employee-details img {
        width: 80px;
        height: 80px;
        float: left;
        margin-right: 20px;
        border: 1px solid #ccc;
    }

    .employee-details p {
        margin: 5px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 5px;
        text-align: left;
    }

    th {
        background-color: #f0f0f0;
        font-weight: bold;
    }

    .signature {
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
    }

    .signature p {
        margin: 5px 0;
    }

    .footer {
        text-align: center;
        font-style: italic;
        margin-top: 20px;
        font-size: 11px;
    }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/kvb-logo.png') }}"
            alt="{{ $employeePayroll->payroll->business->name ?? 'Business' }} Logo">
        <h1>{{ $employeePayroll->payroll->business->name ?? 'KENYA VETERINARY BOARD' }}</h1>
        <div class="business-details">
            <p>{{ $employeePayroll->payroll->business->address ?? 'P.O. Box 12345, Nairobi, Kenya' }}</p>
            <p>Email: {{ $employeePayroll->payroll->business->email ?? 'info@kvb.co.ke' }} | Phone:
                {{ $employeePayroll->payroll->business->phone ?? '+254 20 1234567' }}</p>
        </div>
        <h2>Payslip for the month of
            {{ Carbon\Carbon::create($employeePayroll->payroll->payrun_year, $employeePayroll->payroll->payrun_month)->format('F (m), Y') }}
        </h2>
    </div>

    <div class="employee-details">
        @if($employeePayroll->employee->user->profile_photo_path)
        <img src="{{ public_path('storage/' . $employeePayroll->employee->user->profile_photo_path) }}"
            alt="Employee Photo">
        @endif
        <p><strong>Name:</strong> {{ $employeePayroll->employee->user->name ?? 'N/A' }}</p>
        <p><strong>Employee No:</strong> {{ $employeePayroll->employee->employee_code ?? 'N/A' }}</p>
        <p><strong>Department:</strong> {{ $employeePayroll->employee->employmentDetails->department->name ?? 'N/A' }}
        </p>
        <p><strong>Location:</strong>
            {{ $employeePayroll->payroll->location ? $employeePayroll->payroll->location->name : $employeePayroll->payroll->business->name . ' (All Locations)' }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Taxation</th>
                <th>Pay ({{ $employeePayroll->payroll->currency ?? 'KES' }})</th>
                <th>EUR (R: 140)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Salary</td>
                <td>{{ number_format($employeePayroll->basic_salary, 2) }}</td>
                <td>{{ number_format($employeePayroll->basic_salary, 2) }}</td>
                <td>{{ number_format($employeePayroll->basic_salary / 140, 2) }}</td>
            </tr>
            @if($employeePayroll->allowances)
            @php
            $allowances = json_decode($employeePayroll->allowances, true);
            $allowances = is_array($allowances) ? $allowances : [];
            @endphp
            @foreach($allowances as $allowance)
            @if(is_array($allowance))
            <tr>
                <td>{{ $allowance['name'] ?? 'Unknown Allowance' }}
                    {{ $allowance['is_taxable'] ? '' : '(non taxable)' }}</td>
                <td>{{ $allowance['is_taxable'] ? number_format($allowance['amount'], 2) : '-' }}</td>
                <td>{{ number_format($allowance['amount'], 2) }}</td>
                <td>{{ number_format($allowance['amount'] / 140, 2) }}</td>
            </tr>
            @endif
            @endforeach
            @endif
            @if($employeePayroll->overtime > 0)
            <tr>
                <td>Overtime</td>
                <td>{{ number_format($employeePayroll->overtime, 2) }}</td>
                <td>{{ number_format($employeePayroll->overtime, 2) }}</td>
                <td>{{ number_format($employeePayroll->overtime / 140, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>Gross Pay</strong></td>
                <td><strong>{{ number_format($employeePayroll->gross_pay, 2) }}</strong></td>
                <td><strong>{{ number_format($employeePayroll->gross_pay, 2) }}</strong></td>
                <td><strong>{{ number_format($employeePayroll->gross_pay / 140, 2) }}</strong></td>
            </tr>
            <tr>
                <td>SHIF</td>
                <td>{{ number_format($employeePayroll->shif, 2) }}</td>
                <td>{{ number_format($employeePayroll->shif, 2) }}</td>
                <td>{{ number_format($employeePayroll->shif / 140, 2) }}</td>
            </tr>
            <tr>
                <td>NSSF</td>
                <td>{{ number_format($employeePayroll->nssf, 2) }}</td>
                <td>{{ number_format($employeePayroll->nssf, 2) }}</td>
                <td>{{ number_format($employeePayroll->nssf / 140, 2) }}</td>
            </tr>
            <tr>
                <td>Housing Levy</td>
                <td>{{ number_format($employeePayroll->housing_levy, 2) }}</td>
                <td>{{ number_format($employeePayroll->housing_levy, 2) }}</td>
                <td>{{ number_format($employeePayroll->housing_levy / 140, 2) }}</td>
            </tr>
            @if($employeePayroll->deductions)
            @php
            $deductions = json_decode($employeePayroll->deductions, true);
            $deductions = is_array($deductions) ? $deductions : [];
            @endphp
            @foreach($deductions as $key => $deduction)
            @if(!in_array($key, ['shif', 'nssf', 'paye', 'housing_levy', 'helb']))
            @if(is_array($deduction))
            <tr>
                <td>{{ $deduction['name'] ?? $key }}</td>
                <td>{{ number_format($deduction['amount'], 2) }}</td>
                <td>{{ number_format($deduction['amount'], 2) }}</td>
                <td>{{ number_format($deduction['amount'] / 140, 2) }}</td>
            </tr>
            @endif
            @endif
            @endforeach
            @endif
            <tr>
                <td><strong>Deductions Before Tax</strong></td>
                <td><strong>{{ number_format($employeePayroll->shif + $employeePayroll->nssf + $employeePayroll->housing_levy, 2) }}</strong>
                </td>
                <td><strong>{{ number_format($employeePayroll->shif + $employeePayroll->nssf + $employeePayroll->housing_levy, 2) }}</strong>
                </td>
                <td><strong>{{ number_format(($employeePayroll->shif + $employeePayroll->nssf + $employeePayroll->housing_levy) / 140, 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td>Taxable Income</td>
                <td>{{ number_format($employeePayroll->taxable_income, 2) }}</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Personal Relief</td>
                <td>{{ number_format($employeePayroll->personal_relief, 2) }}</td>
                <td>{{ number_format($employeePayroll->personal_relief, 2) }}</td>
                <td>{{ number_format($employeePayroll->personal_relief / 140, 2) }}</td>
            </tr>
            <tr>
                <td>Tax Relief</td>
                <td>{{ number_format($employeePayroll->insurance_relief + $employeePayroll->mortgage_relief + $employeePayroll->hosp_relief, 2) }}
                </td>
                <td>{{ number_format($employeePayroll->insurance_relief + $employeePayroll->mortgage_relief + $employeePayroll->hosp_relief, 2) }}
                </td>
                <td>{{ number_format(($employeePayroll->insurance_relief + $employeePayroll->mortgage_relief + $employeePayroll->hosp_relief) / 140, 2) }}
                </td>
            </tr>
            <tr>
                <td>PAYE Tax</td>
                <td>{{ number_format($employeePayroll->paye, 2) }}</td>
                <td>{{ number_format($employeePayroll->paye, 2) }}</td>
                <td>{{ number_format($employeePayroll->paye / 140, 2) }}</td>
            </tr>
            @if($employeePayroll->reliefs)
            @php
            $reliefs = json_decode($employeePayroll->reliefs, true);
            $reliefs = is_array($reliefs) ? $reliefs : [];
            @endphp
            @foreach($reliefs as $relief)
            @if(is_array($relief) && !in_array($relief['name'], ['Personal Relief', 'Insurance Relief', 'Mortgage
            Relief', 'HOSP Relief']))
            <tr>
                <td>{{ $relief['name'] }}</td>
                <td>{{ number_format($relief['amount'], 2) }}</td>
                <td>{{ number_format($relief['amount'], 2) }}</td>
                <td>{{ number_format($relief['amount'] / 140, 2) }}</td>
            </tr>
            @endif
            @endforeach
            @endif
            @if($employeePayroll->deductions)
            @foreach($deductions as $key => $deduction)
            @if($key === 'helb')
            <tr>
                <td>HELB</td>
                <td>{{ number_format(is_array($deduction) ? $deduction['amount'] : $deduction, 2) }}</td>
                <td>{{ number_format(is_array($deduction) ? $deduction['amount'] : $deduction, 2) }}</td>
                <td>{{ number_format((is_array($deduction) ? $deduction['amount'] : $deduction) / 140, 2) }}</td>
            </tr>
            @endif
            @endforeach
            @endif
            <tr>
                <td>Deductions After Tax</td>
                <td>{{ number_format($employeePayroll->deductions_after_tax, 2) }}</td>
                <td>{{ number_format($employeePayroll->deductions_after_tax, 2) }}</td>
                <td>{{ number_format($employeePayroll->deductions_after_tax / 140, 2) }}</td>
            </tr>
            <tr>
                <td>Loan Repayment</td>
                <td>{{ number_format($employeePayroll->loan_repayment, 2) }}</td>
                <td>{{ number_format($employeePayroll->loan_repayment, 2) }}</td>
                <td>{{ number_format($employeePayroll->loan_repayment / 140, 2) }}</td>
            </tr>
            <tr>
                <td>Advance Recovery</td>
                <td>{{ number_format($employeePayroll->advance_recovery, 2) }}</td>
                <td>{{ number_format($employeePayroll->advance_recovery, 2) }}</td>
                <td>{{ number_format($employeePayroll->advance_recovery / 140, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Net Pay</strong></td>
                <td><strong>{{ number_format($employeePayroll->net_pay, 2) }}</strong></td>
                <td><strong>{{ number_format($employeePayroll->net_pay, 2) }}</strong></td>
                <td><strong>{{ number_format($employeePayroll->net_pay / 140, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <p>Employer's Signature: ___________________________</p>
        <p>Employee's Signature: ___________________________</p>
    </div>

    <div class="footer">
        <p>Thank you for your service</p>
    </div>
</body>

</html>