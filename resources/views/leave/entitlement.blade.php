<x-app-layout>
    <form method="POST" id="leaveEntitlementsForm">
        @csrf
        <div class="row g-20">

            <!-- Form for general leave entitlement -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="leave_period_id" class="form-label">Leave Period</label>
                                <select name="leave_period_id" id="leave_period_id" class="form-select" required>
                                    <option value="" disabled selected>Select Leave Period</option>
                                    @foreach ($leavePeriods as $leavePeriod)
                                        <option value="{{ $leavePeriod->id }}">{{ $leavePeriod->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="department" class="form-label">Department</label>
                                <select name="department" id="department" class="form-select">
                                    <option value="all">All Departments</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->slug }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="job_category" class="form-label">Job Category</label>
                                <select name="job_category" id="job_category" class="form-select">
                                    <option value="all">All Job Categories</option>
                                    @foreach ($jobCategories as $jobCategory)
                                        <option value="{{ $jobCategory->slug }}">{{ $jobCategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="employment_term" class="form-label">Employment Term</label>
                                <select name="employment_term" id="employment_term" class="form-select">
                                    <option value="all">All Terms</option>
                                    <option value="permanent">Permanent</option>
                                    <option value="contract">Contract</option>
                                    <option value="temporary">Temporary</option>
                                    <option value="internship">Internship</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <h6 class="mb-3">Check one or more employees</h6>

                            <div id="employee-checkboxes" class="form-group">
                                <!-- Dynamic employee checkboxes will be appended here -->
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h6>Dynamic Leave Entitlements</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Entitled Days</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="dynamicRows">
                                <!-- Dynamic rows will be appended here -->
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-start mt-3">
                            <button type="button" class="btn btn-outline-primary" id="addRowButton">
                                <i class="bi bi-plus-circle"></i> Add Row First
                            </button>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="button" onclick="saveLeaveEntitlements(this)" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle"></i> Save Entitlement
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    @push('scripts')
        <script src="{{ asset('js/main/leave-entitlement.js') }}" type="module"></script>
        <script src="{{ asset('js/main/filter-employees.js') }}" type="module"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const filters = ['department', 'job_category', 'employment_term'];

                filters.forEach(filter => {
                    document.getElementById(filter).addEventListener('change', function () {
                        triggerFilterEmployees();
                    });
                });

                async function triggerFilterEmployees() {
                    const department = document.getElementById('department').value;
                    const jobCategory = document.getElementById('job_category').value;
                    const employmentTerm = document.getElementById('employment_term').value;

                    const data = {
                        department: department,
                        jobCategory: jobCategory,
                        employmentTerm: employmentTerm,
                    };

                    try {
                        const employees = await filterEmployees(data);

                        const checkboxesContainer = document.getElementById('employee-checkboxes');
                        checkboxesContainer.innerHTML = '';

                        employees.forEach(employee => {
                            const checkbox = `
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="employee_${employee.id}" name="employees[]" value="${employee.id}">
                                    <label class="form-check-label" for="employee_${employee.id}">${employee.name} (${employee.department})</label>
                                </div>
                            `;
                            checkboxesContainer.insertAdjacentHTML('beforeend', checkbox);
                        });
                    } catch (error) {
                        console.error('Error fetching employees:', error);
                    }
                }


                function addRow() {
                    const dynamicRows = document.getElementById('dynamicRows');
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>
                            <select name="leave_type_ids[]" class="form-select">
                                <option value="" disabled selected>Select Leave Type</option>
                                @foreach ($leaveTypes as $leaveType)
                                    <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="entitled_days[]" class="form-control" step="1" placeholder="e.g., 20">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger removeRowButton">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
                    dynamicRows.appendChild(newRow);
                }

                const addRowButton = document.getElementById('addRowButton');
                addRowButton.addEventListener('click', addRow);

                document.getElementById('dynamicRows').addEventListener('click', function (event) {
                    if (event.target.closest('.removeRowButton')) {
                        const row = event.target.closest('tr');
                        row.remove();
                    }
                });

                addRow();
            });
        </script>

    @endpush

</x-app-layout>
