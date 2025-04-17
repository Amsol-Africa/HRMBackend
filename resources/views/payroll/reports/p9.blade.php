<!DOCTYPE html>
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

</html>