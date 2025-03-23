<!DOCTYPE html>
<html>
<head>
    <title>Payslip</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Payslip</h2>
    <p><strong>Name:</strong> {{ $payslip->employee->name }}</p>
    <p><strong>Employee No:</strong> {{ $payslip->employee->employee_code }}</p>

    <table>
        <tr>
            <th>Description</th>
            <th>Amount (KES)</th>
        </tr>
        <tr>
            <td>Basic Salary</td>
            <td>{{ $payslip->basic_salary }}</td>
        </tr>
        <tr>
            <td>Housing Allowance</td>
            <td>{{ $payslip->housing_allowance }}</td>
        </tr>
        <tr>
            <td>Gross Pay</td>
            <td>{{ $payslip->gross_pay }}</td>
        </tr>
        <tr>
            <td>NHIF</td>
            <td>{{ $payslip->nhif }}</td>
        </tr>
        <tr>
            <td>NSSF</td>
            <td>{{ $payslip->nssf }}</td>
        </tr>
        <tr>
            <td>Housing Levy</td>
            <td>{{ $payslip->housing_levy }}</td>
        </tr>
        <tr>
            <td>PAYE</td>
            <td>{{ $payslip->paye }}</td>
        </tr>
        <tr>
            <td>Net Pay</td>
            <td><strong>{{ $payslip->net_pay }}</strong></td>
        </tr>
    </table>
</body>
</html>
