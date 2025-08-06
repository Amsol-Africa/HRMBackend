{{-- <!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>P9 Form - {{ $year }}</title>
    <style>
    body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 11px;
        /* Slightly reduced for better fit */
        margin: 0;
        padding: 0;
    }

    .container {
        width: 100%;
        padding: 15px;
        /* Reduced padding to save space */
        box-sizing: border-box;
    }

    .header {
        text-align: center;
        margin-bottom: 10px;
        /* Reduced margin */
    }

    .header h2 {
        font-size: 14px;
        /* Slightly smaller to save space */
        margin: 0;
        font-weight: bold;
    }

    .header p {
        margin: 2px 0;
        /* Tighter spacing */
        font-size: 11px;
    }

    .employee-details {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        /* Reduced margin */
    }

    .employee-details .left,
    .employee-details .right {
        width: 48%;
    }

    .employee-details p {
        margin: 1px 0;
        /* Tighter spacing */
        font-size: 11px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        /* Reduced margin */
    }

    th,
    td {
        border: 1px solid #000;
        padding: 3px;
        /* Reduced padding to save space */
        text-align: right;
        vertical-align: middle;
        font-size: 10px;
        /* Slightly smaller to fit table */
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .text-left {
        text-align: left;
    }

    .totals {
        font-weight: bold;
    }

    .footer {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        /* Reduced font size for footer */
        margin-top: 10px;
        /* Reduced margin */
        line-height: 1.2;
        /* Tighter line spacing */
    }

    .footer .left,
    .footer .right {
        width: 48%;
    }

    .footer p {
        margin: 1px 0;
        /* Tighter spacing */
    }

    .page-break {
        page-break-after: always;
    }
    </style>
</head>

<body>
    @foreach($data as $item)
    <div class="container {{ $loop->last ? '' : 'page-break' }}">
        <div class="header">
            <h2>KENYA REVENUE AUTHORITY</h2>
            <p>DOMESTIC TAXES DEPARTMENT</p>
            <p>TAX DEDUCTION CARD YEAR {{ $year }}</p>
        </div>

        <div class="employee-details">
            <div class="left">
                <p>Employer's Name: {{ $business->company_name ?? $business->name }}</p>
                <p>Employee's Main Name: {{ $item['employee_name'] }}</p>
                <p>Employee's Other Name:
                    <!-- Add logic if available -->
                </p>
            </div>
            <div class="right">
                <p>Employer's PIN: {{ $business->tax_pin_no ?? 'N/A' }}</p>
                <p>Employee's PIN: {{ $item['tax_no'] }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-left">Month</th>
                    <th>A<br>Basic Salary</th>
                    <th>B<br>Benefits - Non Cash</th>
                    <th>C<br>Value of quarters</th>
                    <th>D<br>Total gross pay</th>
                    <th colspan="3">Defined contribution retirement scheme</th>
                    <th>F<br>Owner occupied interest</th>
                    <th>G<br>Retirement contribution & owner occupied interest</th>
                    <th>H<br>Chargeable pay</th>
                    <th>J<br>Tax charged</th>
                    <th colspan="2">K</th>
                    <th>L<br>Pay tax (J-K)</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>E1<br>30% of A</th>
                    <th>E2<br>Actual</th>
                    <th>E3<br>Fixed</th>
                    <th></th>
                    <th>The lowest of E added to F</th>
                    <th></th>
                    <th></th>
                    <th>Personal relief</th>
                    <th>Insurance relief</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 12; $i++) <tr>
                    <td class="text-left">{{ \DateTime::createFromFormat('!m', $i)->format('F') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['basic_salary'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['benefits_non_cash'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['value_of_quarters'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['total_gross_pay'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['retirement_e1'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['retirement_e2'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['retirement_e3'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['owner_occupied_interest'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['retirement_contribution'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['chargeable_pay'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['tax_charged'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['personal_relief'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['insurance_relief'], 0, '.', ',') }}</td>
                    <td>{{ number_format($item['monthly_data'][$i]['paye'], 0, '.', ',') }}</td>
                    </tr>
                    @endfor
                    <tr class="totals">
                        <td class="text-left">Total</td>
                        <td>{{ number_format($item['totals']['basic_salary'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['benefits_non_cash'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['value_of_quarters'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['total_gross_pay'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['retirement_e1'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['retirement_e2'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['retirement_e3'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['owner_occupied_interest'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['retirement_contribution'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['chargeable_pay'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['tax_charged'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['personal_relief'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['insurance_relief'], 0, '.', ',') }}</td>
                        <td>{{ number_format($item['totals']['paye'], 0, '.', ',') }}</td>
                    </tr>
            </tbody>
        </table>

        <div class="footer">
            <div class="left">
                <p><strong>TOTAL CHARGEABLE PAY (COL. H):</strong>
                    {{ number_format($item['totals']['chargeable_pay'], 0, '.', ',') }}
                </p>
                <p><strong>TOTAL CHARGEABLE PAY (COL. L):</strong>
                    {{ number_format($item['totals']['paye'], 0, '.', ',') }}
                </p>
                <p><strong>IMPORTANT</strong></p>
                <p>1. Use P9A (a) For all liable employees and where director/employee received benefits in addition to
                    cash emoluments</p>
                <p>(b) Where an employee is eligible to deduction on owner occupier interest</p>
                <p>2. Deductible interest in respect of any month must not exceed Kshs 12,500/= (See back of this card
                    for further information required by the Department).</p>
            </div>
            <div class="right">
                <p>b). Attach</p>
                <p>Photostat copy of interest certificate and statement of account from the Financial Institution.</p>
                <p>The DECLARATION duly signed by the employee.</p>
                <p>NAMES OF FINANCIAL INSTITUTION ADVANCING MORTGAGE LOAN ___________________________</p>
                <p>L R NO. OF OWNER OCCUPIED PROPERTY: ___________________________</p>
                <p>DATE OF OCCUPATION OF HOUSE: ___________________________</p>
            </div>
        </div>
    </div>
    @endforeach
</body>

</html> --}}

<!DOCTYPE html>
<html>
<head>
    <title>KRA P9A Form - {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        th, td {
            border: 1px solid black;
            padding: 3px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-left {
            text-align: left;
        }
        h3 {
            text-align: center;
            margin-bottom: 5px;
        }
        .note {
            font-size: 9px;
            margin-top: 30px;
            line-height: 1.5;
        }
        .totals {
            margin-top: 20px;
            font-size: 10px;
        }
        .employee-details {
            margin-bottom: 10px;
        },
        table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10px;
}

th, td {
    border: 1px solid black;
    padding: 4px;
    text-align: center;
}

    </style>
</head>
<body>
    <h3><strong>KENYA REVENUE AUTHORITY</strong></h3>
    <h3>DOMESTIC TAXES DEPARTMENT</h3>
    <h3><strong>P9A FORM - {{ $year }}</strong></h3>

    <div class="employee-details">
        <p><strong>Employer's Name:</strong> {{ $data[0]['employee_name'] }} &nbsp;&nbsp;&nbsp;&nbsp; <strong>Employer's PIN:</strong> {{ $data[0]['tax_no'] }}</p>
        <p><strong>Employee's Main Name:</strong> {{ $data[0]['main_name'] }} &nbsp;&nbsp;&nbsp;&nbsp; <strong>Employee's PIN:</strong> {{ $data[0]['pin'] }}</p>
        <p><strong>Employee's NSSF:</strong> {{ $data[0]['nssf'] }} &nbsp;&nbsp;&nbsp;&nbsp; <strong>Employee's SHIF:</strong> {{ $data[0]['shif'] }}</p>
    </div>

    <table>
   <thead>
    <tr>
        <th rowspan="2">Month</th>
        <th rowspan="2">Basic Salary</th>
        <th rowspan="2">Benefits - Non Cash</th>
        <th rowspan="2">Value of Quarters</th>
        <th rowspan="2">Total Gross Pay</th>
        <th colspan="3">Defined Contribution Retirement Scheme</th>
        <th rowspan="2">Affordable Housing Levy (AHL)</th>
        <th rowspan="2">Social Health Insurance Fund (SHIF)</th>
        <th rowspan="2">Post Retirement Medical Fund (PRMF)</th>
        <th rowspan="2">Owner Occupied Interest</th>
        <th rowspan="2">Total Deductions<br><small>(Lower of E+F+G+H+I)</small></th>
        <th rowspan="2">Chargeable Pay<br><small>(D - J)</small></th>
        <th rowspan="2">Tax Charged</th>
        <th rowspan="2">Personal Relief</th>
        <th rowspan="2">Insurance Relief</th>
        <th rowspan="2">PAYE Tax<br><small>(L - M - N)</small></th>
    </tr>
    <tr>
        <th>E1<br>30% of A</th>
        <th>E2<br>Actual</th>
        <th>E3<br>Fixed</th>
    </tr>
    <tr>
        <th></th>
        <th>A</th>
        <th>B</th>
        <th>C</th>
        <th>D</th>
        <th>E1</th>
        <th>E2</th>
        <th>E3</th>
        <th>F</th>
        <th>G</th>
        <th>H</th>
        <th>I</th>
        <th>J</th>
        <th>K</th>
        <th>L</th>
        <th>M</th>
        <th>N</th>
        <th>O</th>
    </tr>
</thead>

        <tbody>
            @php
                $months = [
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                ];

                $totalCols = array_fill_keys([
                    'basic_salary', 'benefits_non_cash', 'value_of_quarters', 'total_gross_pay',
                    'retirement_e1', 'retirement_e2', 'retirement_e3', 'housing_levy', 'shif/NHIF', 'prmf',
                    'owner_occupied_interest', 'total_deductions', 'chargeable_pay', 'tax_charged',
                    'personal_relief', 'insurance_relief', 'paye'
                ], 0);
            @endphp

            @foreach($months as $monthNumber => $monthName)
                @php
                    $row = $data[0]['monthly_data'][$monthNumber] ?? [
                        'basic_salary' => 0, 'benefits_non_cash' => 0, 'value_of_quarters' => 0,
                        'total_gross_pay' => 0, 'retirement_e1' => 0, 'retirement_e2' => 0,
                        'retirement_e3' => 0, 'housing_levy' => 0, 'shif' => 0, 'prmf' => 0,
                        'owner_occupied_interest' => 0, 'retirement_contribution' => 0,
                        'chargeable_pay' => 0, 'tax_charged' => 0, 'personal_relief' => 0,
                        'insurance_relief' => 0, 'paye' => 0
                    ];

                    $housing_levy = $row['housing_levy'] ?? ($row['basic_salary'] * 0.015);
                    $shif = $row['shif'] ?? 0;
                    $prmf = $row['prmf'] ?? 0;
                    $owner_occupied_interest = $row['owner_occupied_interest'] ?? 0;

                    $deduction = min($row['retirement_e1'], $row['retirement_e2'], $row['retirement_e3'])
                                + $housing_levy + $shif + $prmf + $owner_occupied_interest;

                    $chargeable = $row['total_gross_pay'] - $deduction;
                    $paye_tax = $row['tax_charged'] - $row['personal_relief'] - $row['insurance_relief'];

                    $row['housing_levy'] = $housing_levy;
                    $row['shif'] = $shif;
                    $row['prmf'] = $prmf;
                    $row['owner_occupied_interest'] = $owner_occupied_interest;
                    $row['total_deductions'] = $deduction;
                    $row['chargeable_pay'] = $chargeable;
                    $row['paye'] = $paye_tax;

                    foreach ($totalCols as $key => $val) {
                        $totalCols[$key] += $row[$key] ?? 0;
                    }
                @endphp

                <tr>
                    <td class="text-left">{{ $monthName }}</td>
                    <td>{{ number_format($row['basic_salary'], 2) }}</td>
                    <td>{{ number_format($row['benefits_non_cash'], 2) }}</td>
                    <td>{{ number_format($row['value_of_quarters'], 2) }}</td>
                    <td>{{ number_format($row['total_gross_pay'], 2) }}</td>
                    <td>{{ number_format($row['retirement_e1'], 2) }}</td>
                    <td>{{ number_format($row['retirement_e2'], 2) }}</td>
                    <td>{{ number_format($row['retirement_e3'], 2) }}</td>
                    <td>{{ number_format($row['housing_levy'], 2) }}</td>
                    <td>{{ number_format($row['shif'], 2) }}</td>
                    <td>{{ number_format($row['prmf'], 2) }}</td>
                    <td>{{ number_format($row['owner_occupied_interest'], 2) }}</td>
                    <td>{{ number_format($row['total_deductions'], 2) }}</td>
                    <td>{{ number_format($row['chargeable_pay'], 2) }}</td>
                    <td>{{ number_format($row['tax_charged'], 2) }}</td>
                    <td>{{ number_format($row['personal_relief'], 2) }}</td>
                    <td>{{ number_format($row['insurance_relief'], 2) }}</td>
                    <td>{{ number_format($row['paye'], 2) }}</td>
                </tr>
            @endforeach

            <tr>
                <td class="text-left"><strong>Total</strong></td>
                @foreach($totalCols as $val)
                    <td><strong>{{ number_format($val, 2) }}</strong></td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <p><strong>To be completed by Employer at end of year</strong></p>
        <p><strong>TOTAL CHARGEABLE PAY (COL. K):</strong> Kshs. {{ number_format($totalCols['chargeable_pay'], 2) }}</p>
        <p><strong>TOTAL TAX (COL. O):</strong> Kshs. {{ number_format($totalCols['paye'], 2) }}</p>
    </div>

    <div class="note">
        <strong>IMPORTANT</strong>
        <ol type="c">
            <li>
                Attach:<br>
                1. Use P9A<br>
                &nbsp;&nbsp;&nbsp;&nbsp;(a) For all liable employees and where director/employee received benefits in addition to cash emoluments.<br>
                &nbsp;&nbsp;&nbsp;&nbsp;(b) Where an employee is eligible to deduction on owner occupier interest.<br>
                &nbsp;&nbsp;&nbsp;&nbsp;(c) Where an employee contributes to a post retirement medical fund.<br>
                2. (i) Photostat copy of interest certificate and statement of account from the Financial Institution.<br>
                &nbsp;&nbsp;&nbsp;&nbsp;(ii) The DECLARATION duly signed by the employee.
            </li>
            <li>
                (a) Deductible interest in respect of any month prior to December 2024 must not exceed Kshs. 25,000/= and commencing December 2024 must not exceed 30,000/=<br>
                (b) Deductible pension contribution prior to December 2024: max 20,000/=; from December 2024: max 30,000/=<br>
                (c) Deductible contribution to PRMF from December 2024 must not exceed 15,000/= per month<br>
                (d) Contributions to SHIF and AHL effective December 2024<br>
                (e) Personal Relief: Kshs. 2,400/month or 28,800/year<br>
                (f) Insurance Relief: 15% of premiums up to Kshs. 5,000/month or 60,000/year
            </li>
        </ol>
    </div>
</body>
</html>
