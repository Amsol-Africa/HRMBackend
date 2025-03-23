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
                                <!-- Existing fields unchanged -->
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
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recover_advances"
                                            value="all" id="recoverAdvances">
                                        <label class="form-check-label" for="recoverAdvances">Recover Advances for All
                                            Employees</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="recover_loans" value="all"
                                            id="recoverLoans">
                                        <label class="form-check-label" for="recoverLoans">Recover Loans for All
                                            Employees</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="pay_overtime" value="all"
                                            id="payOvertime">
                                        <label class="form-check-label" for="payOvertime">Pay Overtime for All
                                            Employees</label>
                                    </div>
                                </div>
                                <input type="hidden" name="exempted_employees" id="exemptedEmployees">
                                <input type="hidden" name="recover_advances_specific" id="recoverAdvancesSpecific">
                                <input type="hidden" name="recover_loans_specific" id="recoverLoansSpecific">
                                <input type="hidden" name="pay_overtime_specific" id="payOvertimeSpecific">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" onclick="fetchEmployees()">Fetch
                                        Employees</button>
                                </div>
                            </div>
                        </form>
                        <div id="payrollTableContainer" class="mt-4"></div>
                        <div id="payrollPreviewContainer" class="mt-4">
                            <div id="previewLoader" class="text-center" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .exempted-row {
            background-color: #e9ecef;
            opacity: 0.6;
        }

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
    @endpush
</x-app-layout>