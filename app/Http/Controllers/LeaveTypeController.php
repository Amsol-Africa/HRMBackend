<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\JobCategory;
use App\Models\LeavePolicy;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;

class LeaveTypeController extends Controller
{
    use HandleTransactions;

    public function index(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $leaveTypes = $business->leaveTypes()->with('leavePolicies')->get();

        return RequestResponse::ok('Leave types fetched successfully.', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requires_approval' => 'required|boolean',
            'is_paid' => 'required|boolean',
            'allowance_accruable' => 'required|boolean',
            'allows_half_day' => 'required|boolean',
            'requires_attachment' => 'required|boolean',
            'max_continuous_days' => 'nullable|integer',
            'min_notice_days' => 'required|integer',
            // Leave policy fields
            'department' => 'required|exists:departments,slug',
            'job_category' => 'required|exists:job_categories,slug',
            'gender_applicable' => 'required|string|in:all,male,female',
            'prorated_for_new_employees' => 'required|boolean',
            'default_days' => 'required|integer',
            'accrual_frequency' => 'required|string|in:monthly,quarterly,yearly',
            'accrual_amount' => 'required|numeric|min:0',
            'max_carryover_days' => 'required|integer',
            'minimum_service_days_required' => 'required|integer',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            $department = Department::findBySlug($validatedData['department']);
            $job_category = JobCategory::findBySlug($validatedData['job_category']);

            $leaveType = $business->leaveTypes()->create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'requires_approval' => $validatedData['requires_approval'],
                'is_paid' => $validatedData['is_paid'],
                'allowance_accruable' => $validatedData['allowance_accruable'],
                'allows_half_day' => $validatedData['allows_half_day'],
                'requires_attachment' => $validatedData['requires_attachment'],
                'max_continuous_days' => $validatedData['max_continuous_days'],
                'min_notice_days' => $validatedData['min_notice_days'],
                'is_active' => true,
            ]);

            $leaveType->leavePolicies()->create([
                'department_id' => $department->id,
                'job_category_id' => $job_category->id,
                'gender_applicable' => $validatedData['gender_applicable'],
                'prorated_for_new_employees' => $validatedData['prorated_for_new_employees'],
                'default_days' => $validatedData['default_days'],
                'accrual_frequency' => $validatedData['accrual_frequency'],
                'accrual_amount' => $validatedData['accrual_amount'],
                'max_carryover_days' => $validatedData['max_carryover_days'],
                'minimum_service_days_required' => $validatedData['minimum_service_days_required'],
                'effective_date' => $validatedData['effective_date'],
                'end_date' => $validatedData['end_date'],
            ]);

            return RequestResponse::created('Leave type and policy created successfully.');
        });
    }

    // Edit a leave type
    public function edit(Request $request, $slug)
    {
        $leaveType = LeaveType::where('slug', $slug)->with('leavePolicies')->firstOrFail();

        return RequestResponse::ok('Leave type fetched successfully.', compact('leaveType'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'leave_type_slug' => 'required|string|exists:leave_types,slug',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requires_approval' => 'required|boolean',
            'is_paid' => 'required|boolean',
            'allowance_accruable' => 'required|boolean',
            'allows_half_day' => 'required|boolean',
            'requires_attachment' => 'required|boolean',
            'max_continuous_days' => 'nullable|integer',
            'min_notice_days' => 'required|integer',
            // Leave policy fields
            'department' => 'required|exists:departments,slug',
            'job_category' => 'required|exists:job_categories,slug',
            'gender_applicable' => 'required|string|in:all,male,female',
            'prorated_for_new_employees' => 'required|boolean',
            'default_days' => 'required|integer',
            'accrual_frequency' => 'required|string|in:monthly,quarterly,yearly',
            'accrual_amount' => 'required|numeric|min:0',
            'max_carryover_days' => 'required|integer',
            'minimum_service_days_required' => 'required|integer',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $leaveType = LeaveType::where('slug', $validatedData['leave_type_slug'])->firstOrFail();
            $department = Department::findBySlug($validatedData['department']);
            $job_category = JobCategory::findBySlug($validatedData['job_category']);

            $leaveType->update([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'requires_approval' => $validatedData['requires_approval'],
                'is_paid' => $validatedData['is_paid'],
                'allowance_accruable' => $validatedData['allowance_accruable'],
                'allows_half_day' => $validatedData['allows_half_day'],
                'requires_attachment' => $validatedData['requires_attachment'],
                'max_continuous_days' => $validatedData['max_continuous_days'],
                'min_notice_days' => $validatedData['min_notice_days'],
            ]);

            $leaveType->leavePolicies()->update([
                'department_id' => $department->id,
                'job_category_id' => $job_category->id,
                'gender_applicable' => $validatedData['gender_applicable'],
                'prorated_for_new_employees' => $validatedData['prorated_for_new_employees'],
                'default_days' => $validatedData['default_days'],
                'accrual_frequency' => $validatedData['accrual_frequency'],
                'accrual_amount' => $validatedData['accrual_amount'],
                'max_carryover_days' => $validatedData['max_carryover_days'],
                'minimum_service_days_required' => $validatedData['minimum_service_days_required'],
                'effective_date' => $validatedData['effective_date'],
                'end_date' => $validatedData['end_date'],
            ]);

            return RequestResponse::ok('Leave type and policy updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'leave_type_slug' => 'required|string|exists:leave_types,slug',
        ]);
        return $this->handleTransaction(function () use ($validatedData) {
            $leaveType = LeaveType::where('slug', $validatedData['leave_type_slug'])->firstOrFail();
            $leaveType->leavePolicies()->delete();
            $leaveType->delete();
            return RequestResponse::ok('Leave type and policies deleted successfully.');
        });
    }
}
