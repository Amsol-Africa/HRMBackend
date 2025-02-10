<x-app-layout>

    <form method="POST" id="leaveEntitlementsForm">
        @csrf
        <div class="row g-20">

            <!-- Selection Panel -->
            <div class="col-md-7">
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
                                <label for="departments" class="form-label">Location</label>
                                <select name="locations[]" id="locations" class="form-select select2-multiple" multiple>
                                    <option selected value="{{ $currentBusiness->slug }}">{{ $currentBusiness->company_name }}</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->slug }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="departments" class="form-label">Departments</label>
                                <select name="departments[]" id="departments" class="form-select select2-multiple" multiple>
                                    <option value="all" selected>All Departments</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->slug }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="job_categories" class="form-label">Job Categories</label>
                                <select name="job_categories[]" id="job_categories" class="form-select select2-multiple" multiple>
                                    <option value="all" selected>All Job Categories</option>
                                    @foreach ($jobCategories as $jobCategory)
                                        <option value="{{ $jobCategory->slug }}">{{ $jobCategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="employment_terms" class="form-label">Employment Terms</label>
                                <select name="employment_terms[]" id="employment_terms" class="form-select select2-multiple" multiple>
                                    <option value="all" selected>All Terms</option>
                                    <option value="permanent">Permanent</option>
                                    <option value="contract">Contract</option>
                                    <option value="temporary">Temporary</option>
                                    <option value="internship">Internship</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <h6 class="mb-3">Select Employees (Optional)</h6>
                            <div id="employee-checkboxes" class="form-group">
                                <!-- Dynamic employee checkboxes will be added here -->
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Leave Type and Entitlements Panel -->
            <div class="col-md-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h6>Leave Type Entitlements</h6>
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
                                <i class="bi bi-plus-circle"></i> Add Leave Type
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

                getLeaveEntitlements()

            });
        </script>

    @endpush

</x-app-layout>
