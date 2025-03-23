<div class="table-responsive">
    <table class="table table-hover table-bordered" id="employeeTable">
        <thead class="bg-light">
            <tr>
                <th>Name</th>
                <th>Basic Salary</th>
                <th>Payment Mode</th>
                <th>Bank Details</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Reliefs</th>
                <th>Loans</th>
                <th>Advances</th>
                <th>Overtime</th>
                <th>Attendance</th>
                <th>Warnings</th>
                <th>Exempt</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
            <tr>
                <td>{{ $employee->user->name ?? 'N/A' }}</td>
                <td>{{ $employee->paymentDetails ? number_format($employee->paymentDetails->basic_salary ?? 0, 2) : '0.00' }}
                    {{ $employee->paymentDetails->currency ?? 'KES' }}
                </td>
                <td>{{ $employee->paymentDetails->payment_mode ?? 'N/A' }}</td>
                <td>{{ $employee->paymentDetails ? ($employee->paymentDetails->bank_name ?? 'N/A') . ' (' . ($employee->paymentDetails->account_number ?? 'N/A') . ')' : 'N/A' }}
                </td>
                <td>{{ $employee->employeeAllowances->map(fn($ea) => $ea->allowance ? "{$ea->allowance->name} (" . number_format($ea->amount ?? 0, 2) . ")" : 'Invalid Allowance')->implode(', ') ?: 'None' }}
                </td>
                <td>{{ $employee->employeeDeductions->map(fn($ed) => $ed->deduction ? "{$ed->deduction->name} (" . number_format($ed->amount ?? 0, 2) . ")" : 'Invalid Deduction')->implode(', ') ?: 'None' }}
                </td>
                <td>{{ $employee->reliefs->map(fn($r) => $r->pivot ? "{$r->name} (" . number_format($r->pivot->amount ?? 0, 2) . ")" : 'Invalid Relief')->implode(', ') ?: 'None' }}
                </td>
                <td>{{ $employee->loans->map(fn($l) => "Loan #{$l->id} (" . number_format($l->amount ?? 0, 2) . ")")->implode(', ') ?: 'None' }}
                </td>
                <td>{{ $employee->advances->map(fn($a) => "Advance on {$a->date->format('Y-m-d')} (" . number_format($a->amount ?? 0, 2) . ")")->implode(', ') ?: 'None' }}
                </td>
                <td>{{ $employee->overtimes->map(fn($o) => "{$o->date->format('Y-m-d')} (" . number_format($o->total_pay ?? 0, 2) . ")")->implode(', ') ?: 'None' }}
                </td>
                <td>Present: {{ $employee->attendances->where('is_absent', false)->count() }} | Absent:
                    {{ $employee->attendances->where('is_absent', true)->count() }}
                </td>
                <td>{{ isset($warnings[$employee->id]) ? '<ul>' . collect($warnings[$employee->id])->map(fn($w) => "<li class='text-danger'>{$w}</li>")->implode('') . '</ul>' : '<span class="text-success">No issues</span>' }}
                </td>
                <td><input type="checkbox" name="exempted_employees[{{ $employee->id }}]" value="1"
                        {{ array_key_exists($employee->id, $options['exempted_employees']) ? 'checked' : '' }}
                        onchange="updateExemptions(this)"></td>
                <td><button class="btn btn-sm btn-primary"
                        onclick="showAdjustmentModal('{{ $employee->id }}', 'employee')">Add Adjustment</button></td>
            </tr>
            @empty
            <tr>
                <td colspan="14" class="text-center">No employees found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <button class="btn btn-primary mt-3" onclick="previewPayroll()" {{ !empty($warnings) ? 'disabled' : '' }}>Preview
        Payroll</button>
</div>

<div class="modal fade" id="adjustmentModal" tabindex="-1" aria-labelledby="adjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-3 border-0 shadow-sm">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="adjustmentModalLabel">Add Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="adjustmentForm">
                    <input type="hidden" id="employeeId" name="employee_id">
                    <input type="hidden" id="scope" name="scope">
                    <input type="hidden" id="scopeId" name="scope_id">
                    <div class="mb-3">
                        <label>Type</label>
                        <select class="form-select" id="adjustmentType" onchange="updateOptions()">
                            <option value="allowance">Allowance</option>
                            <option value="deduction">Deduction</option>
                            <option value="relief">Relief</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" id="adjustmentName"
                            placeholder="Enter custom name or select below">
                        <select class="form-select mt-2" id="adjustmentOptions">
                            <option value="">-- Select Existing --</option>
                            @foreach($allowances as $allowance)
                            <option value="{{ $allowance->name }}" data-type="allowance"
                                data-amount="{{ $allowance->amount }}">{{ $allowance->name }} (Default:
                                {{ $allowance->amount }})
                            </option>
                            @endforeach
                            @foreach($deductions as $deduction)
                            <option value="{{ $deduction->name }}" data-type="deduction"
                                data-amount="{{ $deduction->amount }}">{{ $deduction->name }} (Default:
                                {{ $deduction->amount }})
                            </option>
                            @endforeach
                            @foreach($reliefs as $relief)
                            <option value="{{ $relief->name }}" data-type="relief" data-amount="{{ $relief->amount }}">
                                {{ $relief->name }} (Default: {{ $relief->amount }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Amount</label>
                        <input type="number" class="form-control" id="adjustmentAmount" step="0.01" min="0"
                            placeholder="Enter amount">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveAdjustment()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#employeeTable').DataTable({
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

function showAdjustmentModal(employeeId, scope) {
    $('#employeeId').val(employeeId);
    $('#scope').val(scope);
    $('#scopeId').val(employeeId);
    $('#adjustmentType').val('allowance');
    updateOptions();
    $('#adjustmentModal').modal('show');
}

function updateOptions() {
    const type = $('#adjustmentType').val();
    $('#adjustmentOptions option').hide();
    $('#adjustmentOptions option[data-type="' + type + '"]').show();
    $('#adjustmentOptions').val('');
    $('#adjustmentAmount').val('');
}

$('#adjustmentOptions').on('change', function() {
    const selected = $(this).find(':selected');
    const defaultAmount = selected.data('amount');
    if (defaultAmount) $('#adjustmentAmount').val(defaultAmount);
});

function saveAdjustment() {
    const employeeId = $('#employeeId').val();
    const type = $('#adjustmentType').val();
    const scope = $('#scope').val();
    const scopeId = $('#scopeId').val();
    const name = $('#adjustmentName').val() || $('#adjustmentOptions').val();
    const amount = $('#adjustmentAmount').val();

    if (!name || !amount) {
        Swal.fire('Error', 'Name and amount are required.', 'error');
        return;
    }

    addAdjustment(employeeId, type, scope, scopeId, amount, name);
    $('#adjustmentModal').modal('hide');
}

function updateExemptions(checkbox) {
    const form = document.getElementById('payrollForm');
    const exemptedInputs = form.querySelectorAll('input[name^="exempted_employees["]:checked');
    const exempted = Array.from(exemptedInputs).reduce((acc, input) => {
        const employeeId = input.name.match(/\[(\d+)\]/)[1];
        acc[employeeId] = '1';
        return acc;
    }, {});
    const hiddenInput = form.querySelector('input[name="exempted_employees"]') || document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'exempted_employees';
    hiddenInput.value = JSON.stringify(exempted);
    if (!hiddenInput.parentNode) form.appendChild(hiddenInput);
}
</script>