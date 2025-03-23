<x-app-layout title="{{ $page }}">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <h2 class="fw-bold text-dark mb-4">{{ $page }}</h2>
                <div class="card shadow-sm mb-5 border-0 rounded-3 bg-white">
                    <div class="card-body p-4">
                        <h4 class="fw-semibold text-dark mb-4">Run Payroll</h4>
                        <form id="payrollForm" class="needs-validation" novalidate>
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label>Year</label>
                                    <select name="year" class="form-select" required>
                                        @foreach($years as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label>Month</label>
                                    <select name="month" class="form-select" required>
                                        @foreach($months as $month)
                                        <option value="{{ $month }}">{{ now()->month($month)->monthName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label>Location (Optional)</label>
                                    <select name="location_id" class="form-select">
                                        <option value="">All Locations</option>
                                        @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label>Department (Optional)</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label>Job Category (Optional)</label>
                                    <select name="job_category_id" class="form-select">
                                        <option value="">All Job Categories</option>
                                        @forelse($jobCategories as $jobCategory)
                                        <option value="{{ $jobCategory->id }}">{{ $jobCategory->name }}</option>
                                        @empty
                                        <option value="">No Job Categories Available</option>
                                        @endforelse
                                    </select>
                                </div>

                                <!-- Exempt Employees (Checkbox Section) -->
                                <div class="col-12">
                                    <label>Exempt Employees (Optional)</label>
                                    <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($employees as $employee)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox"
                                                id="exempt_employee_{{ $employee->id }}"
                                                name="exempted_employees[{{ $employee->id }}]" value="1">
                                            <label class="form-check-label" for="exempt_employee_{{ $employee->id }}">
                                                {{ $employee->user->name ?? 'Unnamed' }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Specific Employee Options -->
                                <div class="col-12">
                                    <div class="accordion" id="employeeOptionsAccordion">
                                        <!-- Overtime Options -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingOvertime">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseOvertime"
                                                    aria-expanded="false" aria-controls="collapseOvertime">
                                                    Pay Overtime
                                                </button>
                                            </h2>
                                            <div id="collapseOvertime" class="accordion-collapse collapse"
                                                aria-labelledby="headingOvertime"
                                                data-bs-parent="#employeeOptionsAccordion">
                                                <div class="accordion-body">
                                                    <div class="mb-3">
                                                        <select name="pay_overtime[apply]"
                                                            class="form-select overtime-apply">
                                                            <option value="all">All Employees</option>
                                                            <option value="none">None</option>
                                                            <option value="specific">Specific Employees</option>
                                                        </select>
                                                    </div>
                                                    <div id="overtime-specific" style="display: none;">
                                                        @foreach($employees as $employee)
                                                        <div class="row mb-2 align-items-center">
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input
                                                                        class="form-check-input overtime-employee-checkbox"
                                                                        type="checkbox"
                                                                        id="overtime_employee_{{ $employee->id }}"
                                                                        name="pay_overtime[employees][]"
                                                                        value="{{ $employee->id }}">
                                                                    <label class="form-check-label"
                                                                        for="overtime_employee_{{ $employee->id }}">
                                                                        {{ $employee->user->name ?? 'Unnamed' }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number"
                                                                    name="pay_overtime[amounts][{{ $employee->id }}]"
                                                                    class="form-control overtime-employee-amount"
                                                                    placeholder="Custom Amount" step="0.01" min="0"
                                                                    disabled>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Absenteeism Options -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingAbsenteeism">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseAbsenteeism"
                                                    aria-expanded="false" aria-controls="collapseAbsenteeism">
                                                    Charge Absenteeism
                                                </button>
                                            </h2>
                                            <div id="collapseAbsenteeism" class="accordion-collapse collapse"
                                                aria-labelledby="headingAbsenteeism"
                                                data-bs-parent="#employeeOptionsAccordion">
                                                <div class="accordion-body">
                                                    <div class="mb-3">
                                                        <select name="charge_absenteeism[apply]"
                                                            class="form-select absenteeism-apply">
                                                            <option value="none">None</option>
                                                            <option value="all">All Employees</option>
                                                            <option value="specific">Specific Employees</option>
                                                        </select>
                                                    </div>
                                                    <div id="absenteeism-specific" style="display: none;">
                                                        @foreach($employees as $employee)
                                                        <div class="row mb-2 align-items-center">
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input
                                                                        class="form-check-input absenteeism-employee-checkbox"
                                                                        type="checkbox"
                                                                        id="absenteeism_employee_{{ $employee->id }}"
                                                                        name="charge_absenteeism[employees][]"
                                                                        value="{{ $employee->id }}">
                                                                    <label class="form-check-label"
                                                                        for="absenteeism_employee_{{ $employee->id }}">
                                                                        {{ $employee->user->name ?? 'Unnamed' }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number"
                                                                    name="charge_absenteeism[amounts][{{ $employee->id }}]"
                                                                    class="form-control absenteeism-employee-amount"
                                                                    placeholder="Custom Amount" step="0.01" min="0"
                                                                    disabled>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Advances Options -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingAdvances">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseAdvances"
                                                    aria-expanded="false" aria-controls="collapseAdvances">
                                                    Recover Advances
                                                </button>
                                            </h2>
                                            <div id="collapseAdvances" class="accordion-collapse collapse"
                                                aria-labelledby="headingAdvances"
                                                data-bs-parent="#employeeOptionsAccordion">
                                                <div class="accordion-body">
                                                    <div class="mb-3">
                                                        <select name="recover_advances[apply]"
                                                            class="form-select advances-apply">
                                                            <option value="all">All Employees</option>
                                                            <option value="none">None</option>
                                                            <option value="specific">Specific Employees</option>
                                                        </select>
                                                    </div>
                                                    <div id="advances-specific" style="display: none;">
                                                        @foreach($employees as $employee)
                                                        <div class="row mb-2 align-items-center">
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input
                                                                        class="form-check-input advances-employee-checkbox"
                                                                        type="checkbox"
                                                                        id="advances_employee_{{ $employee->id }}"
                                                                        name="recover_advances[employees][]"
                                                                        value="{{ $employee->id }}">
                                                                    <label class="form-check-label"
                                                                        for="advances_employee_{{ $employee->id }}">
                                                                        {{ $employee->user->name ?? 'Unnamed' }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number"
                                                                    name="recover_advances[amounts][{{ $employee->id }}]"
                                                                    class="form-control advances-employee-amount"
                                                                    placeholder="Custom Amount" step="0.01" min="0"
                                                                    disabled>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Loans Options -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingLoans">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseLoans"
                                                    aria-expanded="false" aria-controls="collapseLoans">
                                                    Recover Loans
                                                </button>
                                            </h2>
                                            <div id="collapseLoans" class="accordion-collapse collapse"
                                                aria-labelledby="headingLoans"
                                                data-bs-parent="#employeeOptionsAccordion">
                                                <div class="accordion-body">
                                                    <div class="mb-3">
                                                        <select name="recover_loans[apply]"
                                                            class="form-select loans-apply">
                                                            <option value="all">All Employees</option>
                                                            <option value="none">None</option>
                                                            <option value="specific">Specific Employees</option>
                                                        </select>
                                                    </div>
                                                    <div id="loans-specific" style="display: none;">
                                                        @foreach($employees as $employee)
                                                        <div class="row mb-2 align-items-center">
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input
                                                                        class="form-check-input loans-employee-checkbox"
                                                                        type="checkbox"
                                                                        id="loans_employee_{{ $employee->id }}"
                                                                        name="recover_loans[employees][]"
                                                                        value="{{ $employee->id }}">
                                                                    <label class="form-check-label"
                                                                        for="loans_employee_{{ $employee->id }}">
                                                                        {{ $employee->user->name ?? 'Unnamed' }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number"
                                                                    name="recover_loans[amounts][{{ $employee->id }}]"
                                                                    class="form-control loans-employee-amount"
                                                                    placeholder="Custom Amount" step="0.01" min="0"
                                                                    disabled>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="temporary_options" id="temporaryOptions">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" onclick="fetchEmployees()">Fetch
                                        Employees</button>
                                </div>
                            </div>
                        </form>
                        <div id="payrollTableContainer" class="mt-4"></div>
                        <div id="payrollPreviewContainer" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }

    .table th,
    .table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .btn-primary,
    .btn-success {
        border-radius: 5px;
        padding: 8px 20px;
    }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('js/main/payroll.js') }}" type="module"></script>
    <script>
    $(document).ready(function() {
        // Generic function to handle employee-specific sections
        function setupEmployeeSpecificSection(sectionName, applyClass, checkboxClass, amountClass,
            specificDivId) {
            const applySelect = $(`.${applyClass}`);
            const specificDiv = $(`#${specificDivId}`);

            function toggleSpecificDiv() {
                specificDiv.toggle(applySelect.val() === 'specific');
                if (applySelect.val() !== 'specific') {
                    specificDiv.find(`.${checkboxClass}`).prop('checked', false);
                    specificDiv.find(`.${amountClass}`).prop('disabled', true).val('');
                }
            }

            applySelect.on('change', toggleSpecificDiv);
            toggleSpecificDiv();

            specificDiv.find(`.${checkboxClass}`).on('change', function() {
                const employeeId = $(this).val();
                const amountInput = specificDiv.find(
                    `input[name="${sectionName}[amounts][${employeeId}]"]`);
                amountInput.prop('disabled', !this.checked);
                if (!this.checked) amountInput.val('');
            });
        }

        setupEmployeeSpecificSection('pay_overtime', 'overtime-apply', 'overtime-employee-checkbox',
            'overtime-employee-amount', 'overtime-specific');
        setupEmployeeSpecificSection('charge_absenteeism', 'absenteeism-apply', 'absenteeism-employee-checkbox',
            'absenteeism-employee-amount', 'absenteeism-specific');
        setupEmployeeSpecificSection('recover_advances', 'advances-apply', 'advances-employee-checkbox',
            'advances-employee-amount', 'advances-specific');
        setupEmployeeSpecificSection('recover_loans', 'loans-apply', 'loans-employee-checkbox',
            'loans-employee-amount', 'loans-specific');

        // Store form data in hidden input on change
        $('#payrollForm').on('change', 'select, input', function() {
            const formData = $('#payrollForm').serializeArray();
            $('#temporaryOptions').val(JSON.stringify(formData));
        });
    });
    </script>
    @endpush
</x-app-layout>