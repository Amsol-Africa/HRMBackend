<x-app-layout>
    <div class="row g-20 mb-4">

        <div class="col-md-7">

            <div class="card">
                <div class="card-body">
                    <form id="processPayroll">
                        <div class="row mb-3">
                            <h6 class="mb-3">Start a Payrun</h6>
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="text" name="start_date" id="start_date" class="form-control datepicker" placeholder="Start Date">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="text" name="end_date" id="end_date" class="form-control datepicker" placeholder="End Date">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Location / Branch</label>
                                <select name="locations[]" id="locations" class="form-select select2-multiple" multiple>
                                    <option value="all">Select</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->slug }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="departments" class="form-label">Departments</label>
                                <select name="departments[]" id="departments" class="form-select select2-multiple" multiple>
                                    <option value="all">All Departments</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->slug }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="job_categories" class="form-label">Job Categories</label>
                                <select name="job_categories[]" id="job_categories" class="form-select select2-multiple" multiple>
                                    <option value="all">All Job Categories</option>
                                    @foreach ($jobCategories as $jobCategory)
                                        <option value="{{ $jobCategory->slug }}">{{ $jobCategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
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
                                    <input type="checkbox" class="form-check-input" id="repay_loans" name="repay_loans" value="repay_loans" checked>
                                    <label class="form-check-label" for="">Repay Loans</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="repay_loans" name="repay_loans" value="repay_loans" checked>
                                    <label class="form-check-label" for="">Recover Advance</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="repay_loans" name="repay_loans" value="repay_loans">
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
                                <button type="button" class="btn btn-primary w-100" onclick="processPayroll(this)"> <i class="bi bi-check-circle"></i> Process Payroll </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>

        <div class="col-md-5">

            <div class="row g-2">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6><i class="bi bi-calendar-week"></i> Period</h6>
                            <p>January 2024</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6><i class="bi bi-calendar-check"></i> Pay Day</h6>
                            <p>31st January 2024</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6><i class="bi bi-people"></i> Employees</h6>
                            <p>50 Employees</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h6><i class="bi bi-cash-stack"></i> Payroll Cost</h6>
                            <p>KES 5,000,000</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <h6><i class="bi bi-wallet2"></i> Net Pay</h6>
                            <p>KES 3,500,000</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h6><i class="bi bi-graph-down"></i> Taxes</h6>
                            <p>KES 1,000,000</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h6><i class="bi bi-arrow-down-circle"></i> Pre-Tax Deductions</h6>
                            <p>KES 300,000</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light text-dark">
                        <div class="card-body">
                            <h6><i class="bi bi-arrow-down-up"></i> Post-Tax Deductions</h6>
                            <p>KES 200,000</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="row g-20">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Employee Summary</h6>

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
