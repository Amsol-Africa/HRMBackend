<!DOCTYPE html>
<html>

<head>
    <title>P9 Form - {{ $year }}</title>
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }

    th,
    td {
        border: 1px solid black;
        padding: 4px;
        text-align: right;
    }

    th {
        background-color: #f2f2f2;
    }

    .text-left {
        text-align: left;
    }
    </style>
</head>

<body>
    <h2>P9 Form - {{ $year }}</h2>
    <p><strong>Employee:</strong> {{ $data['employee_name'] }}</p>
    <p><strong>Tax No:</strong> {{ $data['tax_no'] }}</p>

    <table>
        <thead>
            <tr>
                <th class="text-left">Month</th>
                <th>A: Basic Salary</th>
                <th>D: Gross Pay</th>
                <th>G: Retirement Contribution</th>
                <th>H: Chargeable Pay</th>
                <th>J: Tax Charged</th>
                <th>K: Personal Relief</th>
                <th>PAYE</th>
            </tr>
        </thead>
        <tbody>
            @php
            $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];
            @endphp
            @foreach($data['monthly_data'] as $month => $row)
            <tr>
                <td class="text-left">{{ $months[$month] }}</td>
                <td>{{ number_format($row['basic_salary'], 2) }}</td>
                <td>{{ number_format($row['total_gross_pay'], 2) }}</td>
                <td>{{ number_format($row['retirement_contribution'], 2) }}</td>
                <td>{{ number_format($row['chargeable_pay'], 2) }}</td>
                <td>{{ number_format($row['tax_charged'], 2) }}</td>
                <td>{{ number_format($row['personal_relief'], 2) }}</td>
                <td>{{ number_format($row['paye'], 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td class="text-left"><strong>Total</strong></td>
                <td><strong>{{ number_format($data['totals']['basic_salary'], 2) }}</strong></td>
                <td><strong>{{ number_format($data['totals']['total_gross_pay'], 2) }}</strong></td>
                <td><strong>{{ number_format($data['totals']['retirement_contribution'], 2) }}</strong></td>
                <td><strong>{{ number_format($data['totals']['chargeable_pay'], 2) }}</strong></td>
                <td><strong>{{ number_format($data['totals']['tax_charged'], 2) }}</strong></td>
                <td><strong>{{ number_format($data['totals']['personal_relief'], 2) }}</strong></td>
                <td><strong>{{ number_format($data['totals']['paye'], 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
