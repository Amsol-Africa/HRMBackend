<x-app-layout title="{{ $page }}">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <h2 class="fw-bold text-dark mb-4">{{ $page }}</h2>
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body p-4">
                        <form id="payrollForm" class="needs-validation" novalidate>
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="year" class="form-label fw-medium text-dark">Year <span
                                            class="text-danger">*</span></label>
                                    <select name="year" class="form-select" required>
                                        @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a year.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="month" class="form-label fw-medium text-dark">Month <span
                                            class="text-danger">*</span></label>
                                    <select name="month" id="month" class="form-select shadow-sm border-0 rounded-3"
                                        required>
                                        @foreach($months as $month)
                                        <option value="{{ $month }}" {{ $month == now()->month ? 'selected' : '' }}>
                                            {{ now()->month($month)->monthName }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a month.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="working_days" class="form-label fw-medium text-dark">Number of Working
                                        Days <span class="text-danger">*</span></label>
                                    <input type="number" name="working_days" id="working_days"
                                        class="form-control shadow-sm border-0 rounded-3" min="1" max="31" value="30"
                                        required>
                                    <div class="invalid-feedback">Please enter a valid number of working days (1-31).
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="location_id" class="form-label fw-medium text-dark">Location <span
                                            class="text-muted small">(Optional)</span></label>
                                    <select name="location_id" id="location_id"
                                        class="form-select shadow-sm border-0 rounded-3" onchange="fetchEmployees()">
                                        <option value="">All Locations</option>
                                        @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="department_id" class="form-label fw-medium text-dark">Department <span
                                            class="text-muted small">(Optional)</span></label>
                                    <select name="department_id" id="department_id"
                                        class="form-select shadow-sm border-0 rounded-3" onchange="fetchEmployees()">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="job_category_id" class="form-label fw-medium text-dark">Job Category
                                        <span class="text-muted small">(Optional)</span></label>
                                    <select name="job_category_id" id="job_category_id"
                                        class="form-select shadow-sm border-0 rounded-3" onchange="fetchEmployees()">
                                        <option value="">All Job Categories</option>
                                        @forelse($jobCategories as $jobCategory)
                                        <option value="{{ $jobCategory->id }}">{{ $jobCategory->name }}</option>
                                        @empty
                                        <option value="">No Job Categories Available</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-12 mt-4">
                                    <h5 class="fw-semibold text-dark mb-3">Select Employees for Payroll</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="employeeSelectionTable">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th><input type="checkbox" id="selectAllEmployees" checked
                                                            onchange="toggleSelectAll(this)"></th>
                                                    <th>Name</th>
                                                    <th>Employee Code</th>
                                                    <th>Location</th>
                                                    <th>Department</th>
                                                    <th>Job Category</th>
                                                </tr>
                                            </thead>
                                            <tbody id="employeeTableBody">
                                                @include('payroll._table')
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <input type="hidden" name="exempted_employees" id="exemptedEmployees">
                                <input type="hidden" name="settings_id" value="">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm me-2"
                                        onclick="togglePayrollSettings()">
                                        <i class="fa fa-cog me-2"></i> Configure Payroll Settings
                                    </button>
                                    <button type="button" class="btn btn-success px-5 py-2 rounded-3 shadow-sm"
                                        onclick="processPayroll()">
                                        <i class="fa fa-check me-2"></i> Process Payroll
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div id="payrollSettingsSection" class="mt-4" style="display: none;">
                            <h5 class="fw-semibold text-dark mb-3">Payroll Settings</h5>
                            <div class="card shadow-sm border-0 rounded-3 bg-white">
                                <div class="card-body p-4">
                                    <ul class="nav nav-tabs mb-3" id="payrollItemTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="allowances-tab" data-bs-toggle="tab"
                                                data-bs-target="#allowances-pane" type="button" role="tab"
                                                aria-controls="allowances-pane" aria-selected="true">Allowances</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="deductions-tab" data-bs-toggle="tab"
                                                data-bs-target="#deductions-pane" type="button" role="tab"
                                                aria-controls="deductions-pane"
                                                aria-selected="false">Deductions</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="reliefs-tab" data-bs-toggle="tab"
                                                data-bs-target="#reliefs-pane" type="button" role="tab"
                                                aria-controls="reliefs-pane" aria-selected="false">Reliefs</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="absenteeism-tab" data-bs-toggle="tab"
                                                data-bs-target="#absenteeism-pane" type="button" role="tab"
                                                aria-controls="absenteeism-pane"
                                                aria-selected="false">Absenteeism</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="overtime-tab" data-bs-toggle="tab"
                                                data-bs-target="#overtime-pane" type="button" role="tab"
                                                aria-controls="overtime-pane" aria-selected="false">Overtime</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="loans-tab" data-bs-toggle="tab"
                                                data-bs-target="#loans-pane" type="button" role="tab"
                                                aria-controls="loans-pane" aria-selected="false">Loans</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="advances-tab" data-bs-toggle="tab"
                                                data-bs-target="#advances-pane" type="button" role="tab"
                                                aria-controls="advances-pane" aria-selected="false">Advances</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="payrollItemTabContent">
                                        <div class="tab-pane fade show active" id="allowances-pane" role="tabpanel"
                                            aria-labelledby="allowances-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Employee</th>
                                                            <th>Subscribed Allowances</th>
                                                            <th>Available Allowances</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="allowancesTableBody">
                                                        <tr>
                                                            <td colspan="3" class="text-center">Loading...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="deductions-pane" role="tabpanel"
                                            aria-labelledby="deductions-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Employee</th>
                                                            <th>Subscribed Deductions</th>
                                                            <th>Available Deductions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="deductionsTableBody">
                                                        <tr>
                                                            <td colspan="3" class="text-center">Loading...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="reliefs-pane" role="tabpanel"
                                            aria-labelledby="reliefs-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Employee</th>
                                                            <th>Subscribed Reliefs</th>
                                                            <th>Available Reliefs</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="reliefsTableBody">
                                                        <tr>
                                                            <td colspan="3" class="text-center">Loading...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="absenteeism-pane" role="tabpanel"
                                            aria-labelledby="absenteeism-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Employee</th>
                                                            <th>Absenteeism Charge</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="absenteeismTableBody">
                                                        <tr>
                                                            <td colspan="2" class="text-center">Loading...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="overtime-pane" role="tabpanel"
                                            aria-labelledby="overtime-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Employee</th>
                                                            <th>Overtime Details</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="overtimeTableBody">
                                                        <tr>
                                                            <td colspan="2" class="text-center">Loading...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="loans-pane" role="tabpanel"
                                            aria-labelledby="loans-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Employee</th>
                                                            <th>Subscribed Loans</th>
                                                            <th>Available Loans</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="loansTableBody">
                                                        <tr>
                                                            <td colspan="3" class="text-center">Loading...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="advances-pane" role="tabpanel"
                                            aria-labelledby="advances-tab">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Employee</th>
                                                            <th>Subscribed Advances</th>
                                                            <th>Available Advances</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="advancesTableBody">
                                                        <tr>
                                                            <td colspan="3" class="text-center">Loading...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="button" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm me-2"
                                            onclick="savePayrollSettings()">
                                            <i class="fa fa-save me-2"></i> Save Settings
                                        </button>
                                        <button type="button" class="btn btn-secondary px-5 py-2 rounded-3 shadow-sm"
                                            onclick="togglePayrollSettings()">
                                            <i class="fa fa-times me-2"></i> Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="payrollPreviewContainer" class="mt-4"></div>
                        <div id="previewLoader" class="text-center mt-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/main/payroll-process.js') }}"></script>
    <script>
    function toggleSelectAll(checkbox) {
        const isChecked = $(checkbox).is(':checked');
        $('#employeeTableBody .employee-checkbox').prop('checked', isChecked);
        updateExemptedEmployees();
    }

    function updateExemptedEmployees() {
        const exempted = {};
        $('#employeeTableBody .employee-checkbox').each(function() {
            const employeeId = $(this).data('employee-id');
            exempted[employeeId] = $(this).is(':checked') ? 0 : 1;
        });
        $('#exemptedEmployees').val(JSON.stringify(exempted));
    }

    function fetchEmployees() {
        const formData = new FormData(document.getElementById('payrollForm'));
        $.ajax({
            url: '/payroll/fetch',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.message === 'success') {
                    $('#employeeTableBody').html(response.data.html);
                    updateExemptedEmployees();
                    console.log(response);
                } else {
                    Swal.fire('Error!', response.message || 'Failed to fetch employees.', 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to fetch employees.', 'error');
            }
        });
    }

    function togglePayrollSettings() {
        const $settingsSection = $('#payrollSettingsSection');
        const $form = $('#payrollForm');
        if ($settingsSection.is(':visible')) {
            $settingsSection.hide();
            $form.show();
        } else {
            configurePayrollSettings();
            $settingsSection.show();
            $form.hide();
        }
    }

    $(document).ready(function() {
        updateExemptedEmployees();
        $('#payrollForm').on('submit', function(e) {
            e.preventDefault();
        });
    });
    </script>
    @endpush
</x-app-layout>