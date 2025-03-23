<div class="table-responsive">
    <div id="tableLoader" class="text-center" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <table class="table table-hover table-bordered" id="employeeTable">
        <thead class="bg-light">
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Basic Salary</th>
                <th>Payment Mode</th>
                <th>Bank Details</th>
                <th>Allowances</th>
                <th>Deductions</th>
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
            <tr data-employee-id="{{ $employee->id }}"
                class="{{ array_key_exists($employee->id, $options['exempted_employees'] ?? []) ? 'exempted-row' : '' }}">
                <td>{{ $employee->user?->name ?? 'N/A' }}</td>
                <td>{{ $employee->employee_code ?? 'N/A' }}</td>
                <td>{{ number_format($employee->paymentDetails?->basic_salary ?? 0, 2) }}
                    {{ $employee->paymentDetails?->currency ?? 'KES' }}
                </td>
                <td>{{ $employee->paymentDetails?->payment_mode ?? 'N/A' }}</td>
                <td>{{ $employee->paymentDetails ? ($employee->paymentDetails->bank_name ?? 'N/A') . ' (' . ($employee->paymentDetails->account_number ?? 'N/A') . ')' : 'N/A' }}
                </td>
                <td class="allowances">
                    {{ $employee->employeeAllowances->map(fn($ea) => $ea->allowance ? "{$ea->allowance->name} (" . number_format($ea->amount ?? 0, 2) . ")" : null)->filter()->implode(', ') ?: 'None' }}
                </td>
                <td class="deductions">
                    {{ $employee->employeeDeductions->map(fn($ed) => $ed->deduction ? "{$ed->deduction->name} (" . number_format($ed->amount ?? 0, 2) . ")" : null)->filter()->implode(', ') ?: 'None' }}
                </td>
                <td class="loans">
                    {{ $employee->loans->map(fn($l) => "Loan #{$l->id} (" . number_format(($l->amount ?? 0) - ($l->repayments->sum('amount') ?? 0), 2) . " remaining)")->implode(', ') ?: 'None' }}
                </td>
                <td class="advances">
                    {{ $employee->advances->map(fn($a) => "Advance on {$a->date?->format('Y-m-d')} (" . number_format($a->amount ?? 0, 2) . ")")->implode(', ') ?: 'None' }}
                </td>
                <td class="overtime">
                    {{ $employee->overtimes->map(fn($o) => "{$o->overtime_hours} hrs @ {$o->rate} (" . number_format($o->total_pay ?? 0, 2) . ")")->implode(', ') ?: 'None' }}
                </td>
                <td>Present: {{ $employee->attendances->where('is_absent', false)->count() }} | Absent:
                    {{ $daysInMonth - $employee->attendances->where('is_absent', false)->count() }}
                </td>
                <td>{!! isset($warnings[$employee->id]) ? '<ul>' . collect($warnings[$employee->id])->map(fn($w) => "<li
                            class='text-danger'>{$w}</li>")->implode('') . '</ul>' : '<span class="text-success">No
                        issues</span>' !!}</td>
                <td><input type="checkbox" name="exempted_employees[{{ $employee->id }}]" value="1"
                        {{ array_key_exists($employee->id, $options['exempted_employees'] ?? []) ? 'checked' : '' }}
                        onchange="toggleExemption(this)"></td>
                <td><button class="btn btn-sm btn-primary adjust-btn" data-employee-id="{{ $employee->id }}"
                        data-scope="employee">Adjust</button></td>
            </tr>
            @empty
            <tr>
                <td colspan="14" class="text-center">No employees found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">
        <button class="btn btn-primary" onclick="previewPayroll()" {{ !empty($warnings) ? 'disabled' : '' }}>Preview
            Payroll</button>
    </div>
</div>

<div class="modal fade" id="adjustmentModal" tabindex="-1" aria-labelledby="adjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3 border-0 shadow-sm">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="adjustmentModalLabel">Adjust Employee Payroll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="adjustmentForm">
                    <input type="hidden" id="employeeId" name="employee_id">
                    <input type="hidden" id="scope" name="scope">
                    <input type="hidden" id="scopeId" name="scope_id">
                    <ul class="nav nav-tabs" id="adjustmentTabs" role="tablist">
                        <li class="nav-item"><button class="nav-link active" id="allowances-tab" data-bs-toggle="tab"
                                data-bs-target="#allowances" type="button" role="tab">Allowances</button></li>
                        <li class="nav-item"><button class="nav-link" id="deductions-tab" data-bs-toggle="tab"
                                data-bs-target="#deductions" type="button" role="tab">Deductions</button></li>
                        <li class="nav-item"><button class="nav-link" id="reliefs-tab" data-bs-toggle="tab"
                                data-bs-target="#reliefs" type="button" role="tab">Reliefs</button></li>
                        <li class="nav-item"><button class="nav-link" id="loans-tab" data-bs-toggle="tab"
                                data-bs-target="#loans" type="button" role="tab">Loans</button></li>
                        <li class="nav-item"><button class="nav-link" id="advances-tab" data-bs-toggle="tab"
                                data-bs-target="#advances" type="button" role="tab">Advances</button></li>
                        <li class="nav-item"><button class="nav-link" id="overtime-tab" data-bs-toggle="tab"
                                data-bs-target="#overtime" type="button" role="tab">Overtime</button></li>
                    </ul>
                    <div class="tab-content mt-3" id="adjustmentTabContent">
                        <div class="tab-pane fade show active" id="allowances" role="tabpanel">
                            @foreach($allowances as $allowance)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="allowances[]"
                                    value="{{ $allowance->id }}" id="allowance_{{ $allowance->id }}">
                                <label class="form-check-label"
                                    for="allowance_{{ $allowance->id }}">{{ $allowance->name }}
                                    ({{ number_format($allowance->amount ?? 0, 2) }})</label>
                            </div>
                            @endforeach
                        </div>
                        <div class="tab-pane fade" id="deductions" role="tabpanel">
                            @foreach($deductions as $deduction)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="deductions[]"
                                    value="{{ $deduction->id }}" id="deduction_{{ $deduction->id }}">
                                <label class="form-check-label"
                                    for="deduction_{{ $deduction->id }}">{{ $deduction->name }}
                                    ({{ number_format($deduction->amount ?? 0, 2) }})</label>
                            </div>
                            @endforeach
                        </div>
                        <div class="tab-pane fade" id="reliefs" role="tabpanel">
                            @foreach($reliefs as $relief)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="reliefs[]"
                                    value="{{ $relief->id }}" id="relief_{{ $relief->id }}">
                                <label class="form-check-label" for="relief_{{ $relief->id }}">{{ $relief->name }}
                                    ({{ number_format($relief->amount ?? 0, 2) }})</label>
                            </div>
                            @endforeach
                        </div>
                        <div class="tab-pane fade" id="loans" role="tabpanel">
                            <div id="loanAdjustments"></div>
                        </div>
                        <div class="tab-pane fade" id="advances" role="tabpanel">
                            <div id="advanceAdjustments"></div>
                        </div>
                        <div class="tab-pane fade" id="overtime" role="tabpanel">
                            <div id="overtimeAdjustments"></div>
                        </div>
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
        $('.adjust-btn').on('click', function() {
            const employeeId = $(this).data('employee-id');
            const scope = $(this).data('scope');
            showAdjustmentModal(employeeId, scope);
        });
    });

    function showAdjustmentModal(employeeId, scope) {
        $('#employeeId').val(employeeId);
        $('#scope').val(scope);
        $('#scopeId').val(employeeId);

        // Show loading spinner
        $('#loanAdjustments').html(
            '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
        );
        $('#advanceAdjustments').html(
            '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
        );
        $('#overtimeAdjustments').html(
            '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
        );

        $.ajax({
            url: '{{ route("payroll.employee.adjustments") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                employee_id: employeeId
            },
            success: function(response) {
                const employeeData = response.data && response.data.data ? response.data.data : response.data;
                if (!employeeData) {
                    Swal.fire('Error', response.message || 'Failed to load employee data.', 'error');
                    return;
                }

                const loans = Array.isArray(employeeData.loans) ? employeeData.loans : [];
                const advances = Array.isArray(employeeData.advances) ? employeeData.advances : [];
                const overtimes = Array.isArray(employeeData.overtimes) ? employeeData.overtimes : [];

                // Populate Loans
                const loansHtml = loans.length ? loans.map(loan => {
                    const remaining = parseFloat(loan.amount) - (loan.repayments ? loan.repayments
                        .reduce((sum, r) => sum + parseFloat(r.amount), 0) : 0);
                    return `
                        <div class="form-group mb-2">
                            <label>Loan #${loan.id} (${remaining.toFixed(2)} remaining)</label>
                            <input type="number" class="form-control" name="loans[${loan.id}]" max="${remaining}" min="0" step="0.01" placeholder="Amount to recover">
                        </div>
                    `;
                }).join('') : '<p>No active loans</p>';
                $('#loanAdjustments').html(loansHtml);

                // Populate Advances
                const advancesHtml = advances.length ? advances.map(advance => `
                    <div class="form-group mb-2">
                        <label>Advance on ${advance.date} (${parseFloat(advance.amount).toFixed(2)})</label>
                        <input type="number" class="form-control" name="advances[${advance.id}]" max="${advance.amount}" min="0" step="0.01" placeholder="Amount to recover">
                    </div>
                `).join('') : '<p>No advances</p>';
                $('#advanceAdjustments').html(advancesHtml);

                // Populate Overtime
                const overtimeHtml = overtimes.length ? overtimes.map(overtime => `
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="overtime[${overtime.id}]" value="1" id="overtime_${overtime.id}">
            <label class="form-check-label" for="overtime_${overtime.id}">
                Overtime on ${overtime.date} (${overtime.overtime_hours} hrs @ ${parseFloat(overtime.total_pay / overtime.overtime_hours).toFixed(2)}/hr = ${parseFloat(overtime.total_pay).toFixed(2)})
            </label>
        </div>
    `).join('') : '<p>No overtime</p>';
                $('#overtimeAdjustments').html(overtimeHtml);

                $('#adjustmentTabs a:first').tab('show');
                $('#adjustmentModal').modal('show');
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseJSON);
                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to load employee data.', 'error');
            }
        });
    }

    function saveAdjustment() {
        const employeeId = $('#employeeId').val();
        const scope = $('#scope').val();
        const scopeId = $('#scopeId').val();
        const allowances = $('input[name="allowances[]"]:checked').map(function() {
            return this.value;
        }).get();
        const deductions = $('input[name="deductions[]"]:checked').map(function() {
            return this.value;
        }).get();
        const reliefs = $('input[name="reliefs[]"]:checked').map(function() {
            return this.value;
        }).get();
        const loans = {};
        $('input[name^="loans["]').each(function() {
            const id = this.name.match(/\[(\d+)\]/)[1];
            if (this.value) loans[id] = parseFloat(this.value);
        });
        const advances = {};
        $('input[name^="advances["]').each(function() {
            const id = this.name.match(/\[(\d+)\]/)[1];
            if (this.value) advances[id] = parseFloat(this.value);
        });
        const overtime = {};
        $('input[name^="overtime["]:checked').each(function() {
            const id = this.name.match(/\[(\d+)\]/)[1];
            overtime[id] = true;
        });

        const data = {
            employee_id: employeeId,
            scope: scope,
            scope_id: scopeId,
            allowances,
            deductions,
            reliefs,
            loans,
            advances,
            overtime
        };

        $.ajax({
            url: '{{ route("payroll.adjust") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            success: function(response) {
                if (!response.success) {
                    Swal.fire('Error', response.message || 'Failed to save adjustments.', 'error');
                    return;
                }

                const employeeRow = $(`tr[data-employee-id="${employeeId}"]`);
                if (response.options) {
                    const loansSpecific = response.options.recover_loans?.specific || {};
                    const advancesSpecific = response.options.recover_advances?.specific || {};
                    const overtimeSpecific = response.options.pay_overtime?.specific || {};

                    // Update Allowances
                    employeeRow.find('.allowances').text(response.allowances.length ? response.allowances.join(
                        ', ') : 'None');

                    // Update Deductions
                    employeeRow.find('.deductions').text(response.deductions.length ? response.deductions.join(
                        ', ') : 'None');

                    // Update Loans
                    const currentLoans = employeeRow.find('.loans');
                    const updatedLoans = response.loans.length ? response.loans.map(loan =>
                        `Loan #${loan.id} (${loan.remaining.toFixed(2)} remaining)`
                    ).join(', ') : 'None';
                    currentLoans.text(updatedLoans);

                    // Update Advances
                    const currentAdvances = employeeRow.find('.advances');
                    const updatedAdvances = response.advances.length ? response.advances.map(advance =>
                        `Advance on ${advance.date} (${advance.amount.toFixed(2)})`
                    ).join(', ') : 'None';
                    currentAdvances.text(updatedAdvances);

                    // Update Overtime
                    const currentOvertime = employeeRow.find('.overtime');
                    const updatedOvertime = response.overtimes.length ? response.overtimes.map(ot =>
                        `${ot.hours} hrs @ ${ot.rate} (${ot.total_pay.toFixed(2)})`
                    ).join(', ') : 'None';
                    currentOvertime.text(updatedOvertime);
                }

                // Clear all inputs and checkboxes
                $('#adjustmentForm')[0].reset();
                $('#loanAdjustments, #advanceAdjustments, #overtimeAdjustments').html('');

                $('#recoverLoansSpecific').val(JSON.stringify(loansSpecific));
                $('#recoverAdvancesSpecific').val(JSON.stringify(advancesSpecific));
                $('#payOvertimeSpecific').val(JSON.stringify(overtimeSpecific));

                $('#adjustmentModal').modal('hide');
                Swal.fire('Success', 'Adjustments saved successfully.', 'success');
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to save adjustments.', 'error');
            }
        });
    }

    function toggleExemption(checkbox) {
        const $checkbox = $(checkbox);
        const employeeId = $checkbox.attr('name').match(/\[(\d+)\]/)[1];
        const $row = $checkbox.closest('tr');
        $row.toggleClass('exempted-row', $checkbox.is(':checked'));

        const exempted = {};
        $('input[name^="exempted_employees["]:checked').each(function() {
            const id = this.name.match(/\[(\d+)\]/)[1];
            exempted[id] = '1';
        });
        $('#exemptedEmployees').val(JSON.stringify(exempted));
    }
</script>