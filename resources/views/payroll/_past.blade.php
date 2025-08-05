<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead class="border-bottom">
            <tr>
                <th><input type="checkbox" id="selectAllPayrolls" onclick="toggleSelectAll()"></th>
                <th>Month</th>
                <th>No. of Payslips</th>
                <th>Status</th>
                <th>Emailed</th>
                <th>1/3 Rule</th>
                <th>Location</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrolls as $payroll)
            <tr>
                <td><input type="checkbox" class="payrollCheckbox" value="{{ $payroll->id }}"
                        onclick="updateSelectedPayrolls()"></td>
                <td>{{ now()->month($payroll->payrun_month)->monthName }}
                    ({{ str_pad($payroll->payrun_month, 2, '0', STR_PAD_LEFT) }}), {{ $payroll->payrun_year }}</td>
                <td>{{ $payroll->no_of_payslips }} payslips</td>
                <td>{{ $payroll->status === 'closed' ? 'closed' : 'open' }}</td>
                <td>{{ $payroll->emailed ? '✔' : '✘' }}</td>
                <td>{{ $payroll->third_rule ? '✔' : '✘' }}</td>
                <td>{{ $payroll->location_id && $payroll->location ? $payroll->location->name : $business->company_name }}
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-dark" onclick="viewPayroll({{ $payroll->id }})"><i
                            class="fa fa-eye"></i></button>
                    <button class="btn btn-sm btn-outline-primary" onclick="emailPayslips({{ $payroll->id }})"><i
                            class="fa fa-envelope"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deletePayroll({{ $payroll->id }})"><i
                            class="fa fa-trash"></i></button>
                    <button class="btn btn-sm btn-outline-dark" onclick="closeMonth({{ $payroll->id }})"><i
                            class="fa fa-lock"></i></button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fa fa-info-circle me-2"></i> No payrolls found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>