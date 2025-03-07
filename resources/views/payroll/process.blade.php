<x-app-layout>
    <div class="row g-20 mb-4">

        <div class="col-md-8">

            <div class="card">
                <div class="card-body">
                    <form id="processPayroll">
                        <div class="row mb-3">
                            <h6 class="mb-3">Start a Payrun</h6>

                            <!-- Year Input -->
                            <div class="col-md-6">
                                <label for="payrun_year" class="form-label">Year</label>
                                <input type="number" id="payrun_year" name="payrun_year" class="form-control" min="{{ now()->year - 5 }}" max="{{ now()->year + 1 }}" value="{{ now()->year }}">
                            </div>

                            <!-- Month Dropdown -->
                            <div class="col-md-6">
                                <label for="payrun_month" class="form-label">Month</label>
                                <select id="payrun_month" name="payrun_month" class="form-select">
                                    @foreach(range(1, 12) as $month)
                                        @php
                                            $isDisabled = now()->year == request('payrun_year', now()->year) && $month > now()->month;
                                        @endphp
                                        <option value="{{ $month }}" {{ now()->month == $month ? 'selected' : '' }} {{ $isDisabled ? 'disabled' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Location / Branch</label>
                                <select name="locations[]" id="locations" class="form-select select2-multiple" multiple>
                                    <option value="all">Select</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->slug }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="departments" class="form-label">Departments</label>
                                <select name="departments[]" id="departments" class="form-select select2-multiple" multiple>
                                    <option value="all">All Departments</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->slug }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="job_categories" class="form-label">Job Categories</label>
                                <select name="job_categories[]" id="job_categories" class="form-select select2-multiple" multiple>
                                    <option value="all">All Job Categories</option>
                                    @foreach ($jobCategories as $jobCategory)
                                        <option value="{{ $jobCategory->slug }}">{{ $jobCategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="employment_terms" class="form-label">Employment Terms</label>
                                <select name="employment_terms[]" id="employment_terms" class="form-select select2-multiple" multiple>
                                    <option value="all">All Terms</option>
                                    <option value="permanent">Permanent</option>
                                    <option value="contract">Contract</option>
                                    <option value="temporary">Temporary</option>
                                    <option value="internship">Internship</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="repay_loans" name="repay_loans" value="1" checked>
                                    <label class="form-check-label" for="">Repay Loans</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="recover_advance" name="recover_advance" value="1" checked>
                                    <label class="form-check-label" for="">Recover Advance</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="pay_overtime" name="pay_overtime" value="1">
                                    <label class="form-check-label" for="">Pay Overtime</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <h6 class="mb-3">Selected Employees</h6>
                            <div id="employee-checkboxes" class="form-group">
                                <!-- Dynamic employee checkboxes will be added here -->
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary w-100" onclick="processPayroll(this)"> <i class="bi bi-check-circle me-1"></i> Process Payroll </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>

    </div>

    <div class="row g-20">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3"> <i class="fa-solid fa-people me-2"></i> Employee Summary</h6>

                    <div id="#employeePayslipsContainer">

                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')

        <script src="{{ asset('js/main/payroll.js') }}" type="module"></script>
        <script src="{{ asset('js/main/filter-employees.js') }}" type="module"></script>

        <script>

            document.addEventListener('DOMContentLoaded', function () {

                const yearInput = document.getElementById('payrun_year');
                const monthSelect = document.getElementById('payrun_month');
                const currentYear = new Date().getFullYear();
                const currentMonth = new Date().getMonth() + 1;

                function updateMonthOptions() {
                    const selectedYear = parseInt(yearInput.value);
                    Array.from(monthSelect.options).forEach(option => {
                        const monthValue = parseInt(option.value);
                        option.disabled = (selectedYear === currentYear && monthValue > currentMonth);
                    });
                }

                yearInput.addEventListener('change', updateMonthOptions);
                updateMonthOptions();

                const filters = ['departments', 'job_categories', 'employment_terms', 'locations'];

                filters.forEach(filter => {
                    $('#' + filter).on('select2:select', function () {
                        triggerFilterEmployees();
                    });
                });

                async function triggerFilterEmployees() {
                    const data = {
                        locations: getSelectedValues('locations'),
                        departments: getSelectedValues('departments'),
                        jobCategories: getSelectedValues('job_categories'),
                        employmentTerms: getSelectedValues('employment_terms'),
                    };

                    try {
                        const employees = await filterEmployees(data);

                        const checkboxesContainer = document.getElementById('employee-checkboxes');
                        checkboxesContainer.innerHTML = '';

                        employees.forEach(employee => {
                            const isChecked = employee.checked ? 'checked' : '';

                            const checkbox = `
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="employee_${employee.id}" name="employees[]" value="${employee.id}" ${isChecked}>
                                    <label class="form-check-label" for="employee_${employee.id}">${employee.name} (${employee.department})</label>
                                </div>
                            `;
                            checkboxesContainer.insertAdjacentHTML('beforeend', checkbox);
                        });
                    } catch (error) {
                        console.error('Error fetching employees:', error);
                    }
                }

                function getSelectedValues(elementId) {
                    return Array.from(document.getElementById(elementId).selectedOptions).map(option => option.value);
                }
            });

        </script>

    @endpush

</x-app-layout>
