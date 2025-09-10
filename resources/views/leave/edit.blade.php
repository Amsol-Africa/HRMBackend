
@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Leave Type: {{ $leaveType->name }}</h3>
    <form action="{{ route('leave-types.update', $leaveType->slug) }}" method="POST">
        @csrf
        @method('PUT')

        <input type="hidden" name="leave_type_slug" value="{{ $leaveType->slug }}">

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $leaveType->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description">{{ old('description', $leaveType->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="requires_approval" class="form-label">Requires Approval</label>
            <select class="form-select" id="requires_approval" name="requires_approval" required>
                <option value="1" {{ old('requires_approval', $leaveType->requires_approval) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !old('requires_approval', $leaveType->requires_approval) ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="is_paid" class="form-label">Is Paid</label>
            <select class="form-select" id="is_paid" name="is_paid" required>
                <option value="1" {{ old('is_paid', $leaveType->is_paid) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !old('is_paid', $leaveType->is_paid) ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="allowance_accruable" class="form-label">Allowance Accruable</label>
            <select class="form-select" id="allowance_accruable" name="allowance_accruable" required>
                <option value="1" {{ old('allowance_accruable', $leaveType->allowance_accruable) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !old('allowance_accruable', $leaveType->allowance_accruable) ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="allows_half_day" class="form-label">Allows Half Day</label>
            <select class="form-select" id="allows_half_day" name="allows_half_day" required>
                <option value="1" {{ old('allows_half_day', $leaveType->allows_half_day) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !old('allows_half_day', $leaveType->allows_half_day) ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="requires_attachment" class="form-label">Requires Attachment</label>
            <select class="form-select" id="requires_attachment" name="requires_attachment" required>
                <option value="1" {{ old('requires_attachment', $leaveType->requires_attachment) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !old('requires_attachment', $leaveType->requires_attachment) ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="max_continuous_days" class="form-label">Max Continuous Days</label>
            <input type="number" class="form-control" id="max_continuous_days" name="max_continuous_days" value="{{ old('max_continuous_days', $leaveType->max_continuous_days) }}">
        </div>

        <div class="mb-3">
            <label for="min_notice_days" class="form-label">Min Notice Days</label>
            <input type="number" class="form-control" id="min_notice_days" name="min_notice_days" value="{{ old('min_notice_days', $leaveType->min_notice_days) }}" required>
        </div>

        {{-- Department Dropdown --}}
        <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <select class="form-select" id="department" name="department" required>
                <option value="all" {{ old('department', $leaveType->leavePolicies->first()->department->slug ?? '') == 'all' ? 'selected' : '' }}>All Departments</option>
                @foreach(\App\Models\Department::all() as $department)
                    <option value="{{ $department->slug }}" {{ old('department', $leaveType->leavePolicies->first()->department->slug ?? '') == $department->slug ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Job Category Dropdown --}}
        <div class="mb-3">
            <label for="job_category" class="form-label">Job Category</label>
            <select class="form-select" id="job_category" name="job_category" required>
                <option value="all" {{ old('job_category', $leaveType->leavePolicies->first()->jobCategory->slug ?? '') == 'all' ? 'selected' : '' }}>All Job Categories</option>
                @foreach(\App\Models\JobCategory::all() as $jobCategory)
                    <option value="{{ $jobCategory->slug }}" {{ old('job_category', $leaveType->leavePolicies->first()->jobCategory->slug ?? '') == $jobCategory->slug ? 'selected' : '' }}>
                        {{ $jobCategory->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="gender_applicable" class="form-label">Gender Applicable</label>
            <select class="form-select" id="gender_applicable" name="gender_applicable" required>
                <option value="all" {{ old('gender_applicable', $leaveType->leavePolicies->first()->gender_applicable ?? '') == 'all' ? 'selected' : '' }}>All</option>
                <option value="male" {{ old('gender_applicable', $leaveType->leavePolicies->first()->gender_applicable ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender_applicable', $leaveType->leavePolicies->first()->gender_applicable ?? '') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="prorated_for_new_employees" class="form-label">Prorated for New Employees</label>
            <select class="form-select" id="prorated_for_new_employees" name="prorated_for_new_employees" required>
                <option value="1" {{ old('prorated_for_new_employees', $leaveType->leavePolicies->first()->prorated_for_new_employees ?? '') ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !old('prorated_for_new_employees', $leaveType->leavePolicies->first()->prorated_for_new_employees ?? '') ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="default_days" class="form-label">Default Days</label>
            <input type="number" class="form-control" id="default_days" name="default_days" value="{{ old('default_days', $leaveType->leavePolicies->first()->default_days ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="accrual_frequency" class="form-label">Accrual Frequency</label>
            <select class="form-select" id="accrual_frequency" name="accrual_frequency" required>
                <option value="monthly" {{ old('accrual_frequency', $leaveType->leavePolicies->first()->accrual_frequency ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="quarterly" {{ old('accrual_frequency', $leaveType->leavePolicies->first()->accrual_frequency ?? '') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                <option value="yearly" {{ old('accrual_frequency', $leaveType->leavePolicies->first()->accrual_frequency ?? '') == 'yearly' ? 'selected' : '' }}>Yearly</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="accrual_amount" class="form-label">Accrual Amount</label>
            <input type="number" class="form-control" id="accrual_amount" name="accrual_amount" value="{{ old('accrual_amount', $leaveType->leavePolicies->first()->accrual_amount ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="max_carryover_days" class="form-label">Max Carryover Days</label>
            <input type="number" class="form-control" id="max_carryover_days" name="max_carryover_days" value="{{ old('max_carryover_days', $leaveType->leavePolicies->first()->max_carryover_days ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="minimum_service_days_required" class="form-label">Minimum Service Days Required</label>
            <input type="number" class="form-control" id="minimum_service_days_required" name="minimum_service_days_required" value="{{ old('minimum_service_days_required', $leaveType->leavePolicies->first()->minimum_service_days_required ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="effective_date" class="form-label">Effective Date</label>
            <input type="date" class="form-control" id="effective_date" name="effective_date" value="{{ old('effective_date', $leaveType->leavePolicies->first()->effective_date ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $leaveType->leavePolicies->first()->end_date ?? '') }}">
        </div>

        <button type="submit" class="btn btn-primary">Update Leave Type</button>
        <a href="{{ route('leave-types.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection