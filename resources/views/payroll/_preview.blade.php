<div class="table-responsive">
    <table class="table table-hover table-bordered" id="previewTable">
        <thead class="bg-light">
            <tr>
                <th>Name</th>
                <th>Basic Salary</th>
                <th>Gross Pay</th>
                <th>Overtime</th>
                <th>Allowances</th>
                <th>SHIF</th>
                <th>NSSF</th>
                <th>PAYE</th>
                <th>NHDF</th>
                <th>HELB</th>
                <th>Absenteeism</th>
                <th>Loans</th>
                <th>Advances</th>
                <th>Deductions</th>
                <th>Reliefs</th>
                <th>Net Pay</th>
                <th>Bank Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrollData as $data)
            <tr>
                <td>{{ $data['employee']->user->name ?? 'N/A' }}</td>
                <td>{{ number_format($data['basic_salary'] ?? 0, 2) }} {{ $data['currency'] ?? 'KES' }}</td>
                <td>{{ number_format($data['gross_pay'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['overtime'] ?? 0, 2) }}</td>
                <td>{{ collect($data['allowances'])->map(fn($a) => "{$a['name']} (" . number_format($a['amount'], 2) . ")")->implode(', ') ?: 'None' }}
                </td>
                <td>{{ number_format($data['shif'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['nssf'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['paye'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['housing_levy'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['helb'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['absenteeism_charge'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['loan_repayment'] ?? 0, 2) }}</td>
                <td>{{ number_format($data['advance_recovery'] ?? 0, 2) }}</td>
                <td>{{ collect($data['deductions'])->map(fn($amount, $id) => "Deduction #{$id} (" . number_format($amount, 2) . ")")->implode(', ') ?: 'None' }}
                </td>
                <td>{{ collect($data['reliefs'])->map(fn($r) => "{$r['name']} (" . number_format($r['amount'], 2) . ")")->implode(', ') ?: 'None' }}
                </td>
                <td>{{ number_format($data['net_pay'] ?? 0, 2) }}</td>
                <td>{{ $data['bank_name'] ?? 'N/A' }} ({{ $data['account_number'] ?? 'N/A' }})</td>
            </tr>
            @empty
            <tr>
                <td colspan="17" class="text-center">No data available for preview. Ensure all employees have complete
                    information.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <button class="btn btn-success mt-3" onclick="submitPayroll()">Submit Payroll</button>
</div>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#previewTable').DataTable({
        destroy: true,
        responsive: true,
        pageLength: 10,
        searching: true,
        ordering: true,
        paging: true,
        language: {
            search: "Filter:"
        }
    });
});
</script>