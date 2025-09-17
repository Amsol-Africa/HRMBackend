<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;

class LeaveTypeController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $leaveTypes = $business->leaveTypes()->with('leavePolicies')->get();

        $leaveTypesTable = view('leave._leave_types_table', compact('leaveTypes'))->render();

        return RequestResponse::ok('Leave types fetched successfully.', $leaveTypesTable);
    }

    public function store(Request $request)
    {
        Log::debug('LeaveType store payload', $request->all());

        $validated = $request->validate([
            'name'                              => 'required|string|max:255',
            'description'                       => 'nullable|string',
            'requires_approval'                 => 'required|boolean',
            'is_paid'                           => 'required|boolean',
            'allowance_accruable'               => 'required|boolean',
            'allows_half_day'                   => 'required|boolean',
            'requires_attachment'               => 'required|boolean',
            'max_continuous_days'               => 'nullable|integer',
            'min_notice_days'                   => 'required|integer',
            'department'                        => 'required|string',
            'job_category'                      => 'required|string',
            'gender_applicable'                 => 'required|string|in:all,male,female',
            'prorated_for_new_employees'        => 'required|boolean',
            'default_days'                      => 'required|integer',
            'accrual_frequency'                 => 'required|string|in:monthly,quarterly,yearly',
            'accrual_amount'                    => 'required|numeric|min:0',
            'max_carryover_days'                => 'required|integer',
            'minimum_service_days_required'     => 'required|integer',
            'effective_date'                    => 'required|date',
            'end_date'                          => 'nullable|date',
            // governance/flow fields
            'allows_backdating'                 => 'required|boolean',
            'approval_levels'                   => 'required|integer|min:0',
            'excluded_days'                     => 'nullable|array',
            'excluded_days.*'                   => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_stepwise'                       => 'required|boolean',
            'stepwise_rules'                    => 'nullable|array',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $business = Business::findBySlug(session('active_business_slug'));

            $leaveType = $business->leaveTypes()->create([
                'name'                => $validated['name'],
                'description'         => $validated['description'] ?? null,
                'requires_approval'   => $validated['requires_approval'],
                'is_paid'             => $validated['is_paid'],
                'allowance_accruable' => $validated['allowance_accruable'],
                'allows_half_day'     => $validated['allows_half_day'],
                'requires_attachment' => $validated['requires_attachment'],
                'max_continuous_days' => $validated['max_continuous_days'] ?? null,
                'min_notice_days'     => $validated['min_notice_days'],
                'is_active'           => true,
                // governance/flow fields
                'allows_backdating'   => $validated['allows_backdating'],
                'approval_levels'     => $validated['approval_levels'],
                'excluded_days'       => $validated['excluded_days'] ?? [],
                'is_stepwise'         => $validated['is_stepwise'],
                'stepwise_rules'      => $validated['stepwise_rules'] ?? [],
            ]);

            $departmentIds = ($validated['department'] === 'all')
                ? $business->departments()->pluck('id')->toArray()
                : [Department::findBySlug($validated['department'])->id];

            $jobCategoryIds = ($validated['job_category'] === 'all')
                ? $business->jobCategories()->pluck('id')->toArray()
                : [JobCategory::findBySlug($validated['job_category'])->id];

            $gender = $validated['gender_applicable'] === 'all'
                ? 'all'
                : $validated['gender_applicable'];

            foreach ($departmentIds as $departmentId) {
                foreach ($jobCategoryIds as $jobCategoryId) {
                    $leaveType->leavePolicies()->create([
                        'department_id'                 => $departmentId,
                        'job_category_id'               => $jobCategoryId,
                        'gender_applicable'             => $gender,
                        'prorated_for_new_employees'    => $validated['prorated_for_new_employees'],
                        'default_days'                  => $validated['default_days'],
                        'accrual_frequency'             => $validated['accrual_frequency'],
                        'accrual_amount'                => $validated['accrual_amount'],
                        'max_carryover_days'            => $validated['max_carryover_days'],
                        'minimum_service_days_required' => $validated['minimum_service_days_required'],
                        'effective_date'                => $validated['effective_date'],
                        'end_date'                      => $validated['end_date'] ?? null,
                    ]);
                }
            }

            return RequestResponse::created('Leave type and policies created successfully.');
        });
    }

    /**
     * Unified edit:
     * - POST /leave-types/edit (AJAX) -> returns fragment wrapped in JSON
     * - GET  /business/{business}/leave-types/{slug}/edit -> full page
     */
    public function edit(Request $request, Business $business = null, $slug = null)
    {
        // AJAX branch
        if ($request->isMethod('post')) {
            // Accept any of these param names to be tolerant with existing JS/HTML
            $slugFromRequest = $request->input('slug')
                ?? $request->input('leave')
                ?? $request->input('leave_type_slug');

            $request->merge(['_slug' => $slugFromRequest]);

            $request->validate([
                '_slug' => 'required|string|exists:leave_types,slug',
            ]);

            $leaveType = LeaveType::with(['leavePolicies.department', 'leavePolicies.jobCategory', 'business'])
                ->where('slug', $slugFromRequest)
                ->firstOrFail();

            $biz          = $leaveType->business;
            $departments  = $biz ? $biz->departments : Department::where('business_id', $leaveType->business_id)->get();
            $jobCategories= $biz ? $biz->jobCategories : JobCategory::where('business_id', $leaveType->business_id)->get();

            $html = view('leave.edit', [
                'leaveType'     => $leaveType,
                'departments'   => $departments,
                'jobCategories' => $jobCategories,
                'isAjax'        => true,
            ])->render();

            return RequestResponse::ok('Edit form loaded.', $html);
        }

        // Full-page branch
        $leaveType = LeaveType::where('slug', $slug)
            ->where('business_id', $business->id)
            ->with('leavePolicies')
            ->firstOrFail();

        return view('leave.edit', [
            'leaveType'     => $leaveType,
            'businessSlug'  => $business->slug,
            'departments'   => $business->departments,
            'jobCategories' => $business->jobCategories,
            'isAjax'        => false,
        ]);
    }

    public function show(Request $request)
    {
        $validated = $request->validate([
            'leave_type_slug' => 'required|string|exists:leave_types,slug',
        ]);

        $leaveType = LeaveType::where('slug', $validated['leave_type_slug'])
            ->with('leavePolicies.department', 'leavePolicies.jobCategory')
            ->firstOrFail();

        $leaveTypeDetails = view('leave._leave_type_details', compact('leaveType'))->render();

        return RequestResponse::ok('Leave type fetched successfully.', $leaveTypeDetails);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'leave_type_slug'                   => 'required|string|exists:leave_types,slug',
            'name'                              => 'required|string|max:255',
            'description'                       => 'nullable|string',
            'requires_approval'                 => 'required|boolean',
            'is_paid'                           => 'required|boolean',
            'allowance_accruable'               => 'required|boolean',
            'allows_half_day'                   => 'required|boolean',
            'requires_attachment'               => 'required|boolean',
            'max_continuous_days'               => 'nullable|integer',
            'min_notice_days'                   => 'required|integer',
            'department'                        => 'required|string',
            'job_category'                      => 'required|string',
            'gender_applicable'                 => 'required|string|in:all,male,female',
            'prorated_for_new_employees'        => 'required|boolean',
            'default_days'                      => 'required|integer',
            'accrual_frequency'                 => 'required|string|in:monthly,quarterly,yearly',
            'accrual_amount'                    => 'required|numeric|min:0',
            'max_carryover_days'                => 'required|integer',
            'minimum_service_days_required'     => 'required|integer',
            'effective_date'                    => 'required|date',
            'end_date'                          => 'nullable|date',
            // governance/flow fields
            'allows_backdating'                 => 'required|boolean',
            'approval_levels'                   => 'required|integer|min:0',
            'excluded_days'                     => 'nullable|array',
            'excluded_days.*'                   => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_stepwise'                       => 'required|boolean',
            'stepwise_rules'                    => 'nullable|array',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $leaveType = LeaveType::where('slug', $validated['leave_type_slug'])->firstOrFail();

            $leaveType->update([
                'name'                => $validated['name'],
                'description'         => $validated['description'] ?? null,
                'requires_approval'   => $validated['requires_approval'],
                'is_paid'             => $validated['is_paid'],
                'allowance_accruable' => $validated['allowance_accruable'],
                'allows_half_day'     => $validated['allows_half_day'],
                'requires_attachment' => $validated['requires_attachment'],
                'max_continuous_days' => $validated['max_continuous_days'] ?? null,
                'min_notice_days'     => $validated['min_notice_days'],
                'allows_backdating'   => $validated['allows_backdating'],
                'approval_levels'     => $validated['approval_levels'],
                'excluded_days'       => $validated['excluded_days'] ?? [],
                'is_stepwise'         => $validated['is_stepwise'],
                'stepwise_rules'      => $validated['stepwise_rules'] ?? [],
            ]);

            $business = $leaveType->business;

            $departmentIds = ($validated['department'] === 'all')
                ? $business->departments()->pluck('id')->toArray()
                : [Department::findBySlug($validated['department'])->id];

            $jobCategoryIds = ($validated['job_category'] === 'all')
                ? $business->jobCategories()->pluck('id')->toArray()
                : [JobCategory::findBySlug($validated['job_category'])->id];

            $gender = $validated['gender_applicable'] === 'all'
                ? 'all'
                : $validated['gender_applicable'];

            $policyPayloadBase = [
                'gender_applicable'             => $gender,
                'prorated_for_new_employees'    => $validated['prorated_for_new_employees'],
                'default_days'                  => $validated['default_days'],
                'accrual_frequency'             => $validated['accrual_frequency'],
                'accrual_amount'                => $validated['accrual_amount'],
                'max_carryover_days'            => $validated['max_carryover_days'],
                'minimum_service_days_required' => $validated['minimum_service_days_required'],
                'effective_date'                => $validated['effective_date'],
                'end_date'                      => $validated['end_date'] ?? null,
            ];

            foreach ($departmentIds as $departmentId) {
                foreach ($jobCategoryIds as $jobCategoryId) {
                    $payload = array_merge($policyPayloadBase, [
                        'department_id'   => $departmentId,
                        'job_category_id' => $jobCategoryId,
                    ]);

                    $policy = $leaveType->leavePolicies()
                        ->where('department_id', $departmentId)
                        ->where('job_category_id', $jobCategoryId)
                        ->first();

                    $policy ? $policy->update($payload) : $leaveType->leavePolicies()->create($payload);
                }
            }

            return RequestResponse::ok('Leave type and policy updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'leave_type_slug' => 'required|string|exists:leave_types,slug',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $leaveType = LeaveType::where('slug', $validated['leave_type_slug'])->firstOrFail();

            $leaveType->leavePolicies()->delete();
            $leaveType->delete();

            return RequestResponse::ok('Leave type and policies deleted successfully.');
        });
    }

    public function requests(Request $request, $slug = null)
    {
        $slug = $slug ?? $request->leave_type_slug;
        if (!$slug) abort(404, 'Leave type slug missing.');

        $leaveType = LeaveType::where('slug', $slug)
            ->with(['leavePolicies', 'leaveRequests' => fn($q) => $q->with(['employee.user'])])
            ->firstOrFail();

        return view('leave.leave_type_requests', compact('leaveType'));
    }

    public function getRemainingDays(Request $request)
    {
        $employeeId  = $request->input('employee_id', auth()->user()->employee->id ?? null);
        $leaveTypeId = $request->input('leave_type_id');

        $entitlement = \App\Models\LeaveEntitlement::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->first();

        $remaining = $entitlement ? $entitlement->getRemainingDays() : 0;

        return response()->json(['remaining_days' => $remaining]);
    }
}
