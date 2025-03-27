<div class="table-responsive">
    AXIS <table class="table table-hover table-bordered" id="previewTable">
        <thead class="bg-light">
            <tr>
                <th>Name</th>
                <th>Basic Salary</th>
                <th>Gross Pay</th>
                <th>Overtime</th>
                <th>Allowances</th>
                <th>SHIF</th>
                <th>NSSF</th>
                <th>Taxable Income</th>
                <th>PAYE (Before Reliefs)</th>
                <th>PAYE</th>
                <th>Personal Relief</th>
                <th>Insurance Relief</th>
                <th>Housing Levy</th>
                <th>HELB</th>
                <th>Loans</th>
                <th>Advances</th>
                <th>Deductions</th>
                <th>Net Pay</th>
                <th>Bank Details</th>
                <th>Present Days</th>
                <th>Absent Days</th>
                <th>Days in Month</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrollData as $data)
            <tr>
                <td>{{ $data['employee']->user?->name ?? 'N/A' }}</td>
                <td>{{ number_format($data['basic_salary'] ?? 0, 2) }} {{ $data['currency'] ?? 'KES' }}</td>
                <!-- Fixed key -->
                <td>{{ number_format($data['gross_pay'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['overtime'] ?? 0, 2) }}</td>
                <td>{{ collect($data['allowances'])->map(fn($a) => "{$a['name']} (" . number_format($a['amount'] ?? 0, 2) . ")")->implode(', ') ?: 'None' }}
                </td>
                <td>{{ number_format($data['shif'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['nssf'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['taxable_income'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['paye_before_reliefs'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['paye'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['personal_relief'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['insurance_relief'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['housing_levy'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['helb'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['loan_repayment'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['advance_recovery'] ?? 0, 2) }}</td>
                <td>{{ collect($data['deductions'])->map(fn($d) => "{$d['name']} (" . number_format($d['amount'] ?? 0, 2) . ")")->implode(', ') ?: 'None' }}
                </td>
                <td>{{ number_format($data['net_pay'] ?? 0, 2) }}</td>
                <td>{{ $data['bank_name'] ?? 'N/A' }} ({{ $data['account_number'] ?? 'N/A' }})</td>
                <td>{{ $data['attendance_present'] ?? 0 }}</td>
                <td>{{ $data['attendance_absent'] ?? 0 }}</td>
                <td>{{ $data['days_in_month'] ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="22" class="text-center">No payroll data available</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>