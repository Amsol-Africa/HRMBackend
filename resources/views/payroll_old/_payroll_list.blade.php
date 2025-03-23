<table id="payrollTable" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Payroll Month</th>
            <th>Year</th>
            <th>No. of Employees</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payrolls as $payroll)
            <tr data-id="{{ $payroll->id }}">
                <td>{{ DateTime::createFromFormat('!m', $payroll->payrun_month)->format('F') }}</td>
                <td>{{ $payroll->payrun_year }}</td>
                <td>{{ $payroll->staff }}</td>
                <td>
                    @if ($payroll->employeePayrolls()->count() > 0)
                        <span class="badge bg-success">PROCESSED</span>
                    @else
                        <span class="badge bg-warning">READY</span>
                    @endif
                </td>
                <td>
                    @if ($payroll->employeePayrolls()->count() > 0)
                        <a href="{{ route('business.payroll.payslips', ['business' => $currentBusiness->slug, 'payroll' => $payroll->id]) }}"
                            class="btn btn-info btn-sm">View Payslips</a>
                    @else
                        <a href="#" class="btn btn-primary btn-sm">Run Payroll</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
