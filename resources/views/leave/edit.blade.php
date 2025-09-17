@php
    // Pick a single policy as the "current" selection to seed the form
    $policy = $leaveType->leavePolicies->first();
    $departments = $departments ?? \App\Models\Department::where('business_id', $leaveType->business_id)->get();
    $jobCategories = $jobCategories ?? \App\Models\JobCategory::where('business_id', $leaveType->business_id)->get();
    $excluded = is_array($leaveType->excluded_days ?? null) ? $leaveType->excluded_days : [];
    $formatDate = function($d) { return $d ? \Illuminate\Support\Carbon::parse($d)->format('Y-m-d') : ''; };
@endphp

<div class="container-fluid p-0">
    <h4 class="mb-3">Edit Leave Type: {{ $leaveType->name }}</h4>

    {{-- IMPORTANT: no action attribute – this is submitted via AJAX by saveLeaveType() --}}
    <form id="leaveTypeForm">
        {{-- Hidden slug the JS looks for --}}
        <input type="hidden" name="leave_type_slug" value="{{ $leaveType->slug }}">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="name" value="{{ old('name', $leaveType->name) }}" required>
            </div>

            <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description">{{ old('description', $leaveType->description) }}</textarea>
            </div>

            <div class="col-md-4">
                <label class="form-label">Requires Approval</label>
                <select class="form-select" name="requires_approval" required>
                    <option value="1" @selected(old('requires_approval', $leaveType->requires_approval))>Yes</option>
                    <option value="0" @selected(!old('requires_approval', $leaveType->requires_approval))>No</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Is Paid</label>
                <select class="form-select" name="is_paid" required>
                    <option value="1" @selected(old('is_paid', $leaveType->is_paid))>Yes</option>
                    <option value="0" @selected(!old('is_paid', $leaveType->is_paid))>No</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Allowance Accruable</label>
                <select class="form-select" name="allowance_accruable" required>
                    <option value="1" @selected(old('allowance_accruable', $leaveType->allowance_accruable))>Yes</option>
                    <option value="0" @selected(!old('allowance_accruable', $leaveType->allowance_accruable))>No</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Allows Half Day</label>
                <select class="form-select" name="allows_half_day" required>
                    <option value="1" @selected(old('allows_half_day', $leaveType->allows_half_day))>Yes</option>
                    <option value="0" @selected(!old('allows_half_day', $leaveType->allows_half_day))>No</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Requires Attachment</label>
                <select class="form-select" name="requires_attachment" required>
                    <option value="1" @selected(old('requires_attachment', $leaveType->requires_attachment))>Yes</option>
                    <option value="0" @selected(!old('requires_attachment', $leaveType->requires_attachment))>No</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Max Continuous Days</label>
                <input type="number" class="form-control" name="max_continuous_days" value="{{ old('max_continuous_days', $leaveType->max_continuous_days) }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Min Notice Days</label>
                <input type="number" class="form-control" name="min_notice_days" value="{{ old('min_notice_days', $leaveType->min_notice_days) }}" required>
            </div>

            {{-- Scope selectors --}}
            <div class="col-md-6">
                <label class="form-label">Department</label>
                <select class="form-select" name="department" required>
                    <option value="all" @selected(old('department', optional($policy?->department)->slug) == 'all')>All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->slug }}" @selected(old('department', optional($policy?->department)->slug) == $department->slug)>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Job Category</label>
                <select class="form-select" name="job_category" required>
                    <option value="all" @selected(old('job_category', optional($policy?->jobCategory)->slug) == 'all')>All Job Categories</option>
                    @foreach($jobCategories as $jobCategory)
                        <option value="{{ $jobCategory->slug }}" @selected(old('job_category', optional($policy?->jobCategory)->slug) == $jobCategory->slug)>{{ $jobCategory->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Gender Applicable</label>
                @php $genderOld = old('gender_applicable', $policy->gender_applicable ?? 'all'); @endphp
                <select class="form-select" name="gender_applicable" required>
                    <option value="all" @selected($genderOld === 'all')>All</option>
                    <option value="male" @selected($genderOld === 'male')>Male</option>
                    <option value="female" @selected($genderOld === 'female')>Female</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Prorated for New Employees</label>
                <select class="form-select" name="prorated_for_new_employees" required>
                    @php $prorated = old('prorated_for_new_employees', $policy->prorated_for_new_employees ?? 0); @endphp
                    <option value="1" @selected($prorated)>Yes</option>
                    <option value="0" @selected(!$prorated)>No</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Default Days</label>
                <input type="number" class="form-control" name="default_days" value="{{ old('default_days', $policy->default_days ?? '') }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Accrual Frequency</label>
                @php $freq = old('accrual_frequency', $policy->accrual_frequency ?? 'monthly'); @endphp
                <select class="form-select" name="accrual_frequency" required>
                    <option value="monthly" @selected($freq==='monthly')>Monthly</option>
                    <option value="quarterly" @selected($freq==='quarterly')>Quarterly</option>
                    <option value="yearly" @selected($freq==='yearly')>Yearly</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Accrual Amount</label>
                <input type="number" step="0.01" class="form-control" name="accrual_amount" value="{{ old('accrual_amount', $policy->accrual_amount ?? '') }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Max Carryover Days</label>
                <input type="number" class="form-control" name="max_carryover_days" value="{{ old('max_carryover_days', $policy->max_carryover_days ?? '') }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Minimum Service Days Required</label>
                <input type="number" class="form-control" name="minimum_service_days_required" value="{{ old('minimum_service_days_required', $policy->minimum_service_days_required ?? '') }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Effective Date</label>
                <input type="date" class="form-control" name="effective_date" value="{{ old('effective_date', $formatDate($policy->effective_date ?? null)) }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_date" value="{{ old('end_date', $formatDate($policy->end_date ?? null)) }}">
            </div>

            {{-- Governance / flow (required by validator) --}}
            <div class="col-md-4">
                <label class="form-label">Allows Backdating</label>
                @php $allowsBack = old('allows_backdating', $leaveType->allows_backdating ?? 0); @endphp
                <select class="form-select" name="allows_backdating" required>
                    <option value="1" @selected($allowsBack)>Yes</option>
                    <option value="0" @selected(!$allowsBack)>No</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Approval Levels</label>
                <input type="number" min="0" class="form-control" name="approval_levels" value="{{ old('approval_levels', $leaveType->approval_levels ?? 0) }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Stepwise Approval?</label>
                @php $isStep = old('is_stepwise', $leaveType->is_stepwise ?? 0); @endphp
                <select class="form-select" name="is_stepwise" required>
                    <option value="1" @selected($isStep)>Yes</option>
                    <option value="0" @selected(!$isStep)>No</option>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label d-block">Excluded Days</label>
                @php $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']; @endphp
                <div class="d-flex flex-wrap gap-3">
                    @foreach($days as $d)
                        <label class="form-check-label">
                            <input class="form-check-input me-1" type="checkbox" name="excluded_days[]" value="{{ $d }}" @checked(in_array($d, old('excluded_days', $excluded)))>
                            {{ ucfirst($d) }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="saveLeaveType(this)">Update Leave Type</button>
            <button type="button" class="btn btn-outline-secondary" onclick="$('#leaveTypeFormContainer').empty();">Cancel</button>
        </div>
    </form>
</div>
