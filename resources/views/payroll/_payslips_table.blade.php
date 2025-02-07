<table class="table table-striped table-hover" id="payslipsTable">
    <thead>
        <tr>
            <th>Employee ID</th>
            <th>Basic Salary</th>
            <th>H / Allowance</th>
            <th>Gross Pay</th>
            <th>NHIF</th>
            <th>NSSF</th>
            <th>Housing Levy</th>
            <th>Taxable Income</th>
            <th>PAYE</th>
            <th>Personal Relief</th>
            <th>Pay After Tax</th>
            <th>Deductions After Tax</th>
            <th>Net Pay</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payslips as $payslip)
        <tr>
            <td>{{ $payslip->employee->employee_code ?? 0 }}</td>
            <td>{{ $payslip->basic_salary ?? 0 }}</td>
            <td>{{ $payslip->housing_allowance ?? 0 }}</td>
            <td>{{ $payslip->gross_pay ?? 0 }}</td>
            <td>{{ $payslip->nhif ?? 0 }}</td>
            <td>{{ $payslip->nssf ?? 0 }}</td>
            <td>{{ $payslip->housing_levy ?? 0 }}</td>
            <td>{{ $payslip->taxable_income ?? 0 }}</td>
            <td>{{ $payslip->paye ?? 0 }}</td>
            <td>{{ $payslip->personal_relief ?? 0 }}</td>
            <td>{{ $payslip->pay_after_tax ?? 0 }}</td>
            <td>{{ $payslip->deductions_after_tax ?? 0 }}</td>
            <td>{{ $payslip->net_pay ?? 0 }}</td>
            <td style="gap: 3px">
                <a href="#" class="btn btn-info btn-sm" title="View Details" onclick="viewPayslipDetails('{{ $payslip->id }}', event)">
                    <i class="bi bi-view-list"></i>
                </a>
                <a href="#" class="btn btn-success btn-sm" title="Download">
                    <i class="bi bi-download"></i>
                </a>
                <a href="#" class="btn btn-warning btn-sm" title="Send Email">
                    <i class="bi bi-envelope"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
