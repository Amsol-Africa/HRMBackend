<table class="table table-striped table-hover" id="payslipsTable">
    <thead>
        <tr>
            <th>Employee ID</th>
            <th>Basic Salary</th>
            <th>H / Allowance</th>
            <th>Gross Pay</th>
            <th>NHIF</th>
            <th>NSSF</th>
            <th>Housing <br> Levy</th>
            <th>Taxable <br> Income</th>
            <th>PAYE</th>
            <th>Personal <br> Relief</th>
            <th>Pay After Tax</th>
            <th>Deductions <br> After Tax</th>
            <th>Net Pay</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payslips as $payslip)
        <tr>
            <td>
                <a href="" class="btn btn-outline-primary btn-sm">
                    {{ $payslip->employee->employee_code ?? 0 }}
                </a>
            </td>
            <td>{{ number_format($payslip->basic_salary, 2) ?? 0 }}</td>
            <td>{{ number_format($payslip->housing_allowance, 2) ?? 0 }}</td>
            <td>{{ number_format($payslip->gross_pay, 2) ?? 0 }}</td>
            <td>{{ number_format($payslip->nhif, 2) ?? 0 }}</td>
            <td>{{ number_format($payslip->nssf, 2) ?? 0 }}</td>
            <td>{{ number_format($payslip->housing_levy, 2) ?? 0 }}</td>
            <td>{{ number_format($payslip->taxable_income, 2) ?? 0 }}</td>
            <td>{{ number_format($payslip->paye, 2) ?? 0 }}</td>
            <td>{{ number_format($payslip->personal_relief, 2) ?? 0 }}</td>
            <td>{{ number_format($payslip->pay_after_tax, 2) ?? 0 }}</td>
            <td>{{ number_format($payslip->deductions_after_tax, 2) ?? 0 }}</td>
            <td>{{ number_format($payslip->net_pay, 2) ?? 0 }}</td>
            <td style="gap: 3px">
                <a href="#" class="btn btn-info btn-sm" title="View Details" data-payslip="{{ $payslip->id }}"  onclick="viewPayslipDetails(this)">
                    <i class="bi bi-view-list"></i>
                </a>
                <button type="button" class="btn btn-success btn-sm" title="Download" data-payslip="{{ $payslip->id }}"  onclick="downloadPayslip(this)">
                    <i class="bi bi-download"></i>
                </button>
                <button href="#" class="btn btn-warning btn-sm" title="Email Payslip" data-payslip="{{ $payslip->id }}"  onclick="emailPayslip(this)">
                    <i class="bi bi-envelope-paper"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
