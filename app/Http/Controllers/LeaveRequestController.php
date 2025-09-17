<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\Employee;
use App\Models\LeaveEntitlement;
use App\Models\LeavePolicy;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\LeaveRequestSubmitted;
use App\Notifications\LeaveStatusNotification;

class LeaveRequestController extends Controller
{
    use HandleTransactions;

    /**
     * Fetch leave requests for tabs.
     * - Employees: only their own
     * - Others (HOD/HR/Admin/Head): all in current business
     */
    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Active business not found in session.');
        }

        $status = strtolower($request->get('status', 'pending'));

        $query = LeaveRequest::with(['employee.user', 'leaveType'])
            ->where('business_id', $business->id);

        // Employees see only their own requests
        $user = auth()->user();
        $emp  = $user->employee;
        if ($user->hasRole('business-employee') && $emp) {
            $query->where('employee_id', $emp->id);
        }

        // Filter by tab status
        if (in_array($status, ['pending', 'approved', 'rejected', 'declined'], true)) {
            $query->status($status);
        }

        $leaveRequests   = $query->latest('id')->get();
        $currentBusiness = $business;

        $html = view('leave._leave_requests_table', compact('leaveRequests', 'currentBusiness'))
            ->with('status', $status)
            ->render();

        return RequestResponse::ok('Leave requests fetched successfully.', $html);
    }

    /**
     * Show one leave request.
     * - Employee: only own request
     * - Others: any request in the same business
     */
    public function show(Request $request, $reference = null)
    {
        $ref = $reference ?? $request->get('reference_number');
        if (!$ref) {
            abort(404, 'Reference number missing.');
        }

        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            abort(404, 'Active business not found.');
        }

        $leave = LeaveRequest::with(['employee.user', 'leaveType', 'approvedBy'])
            ->where('business_id', $business->id)
            ->where('reference_number', $ref)
            ->firstOrFail();

        if (!$this->canUserViewLeaveRequest(auth()->user(), $leave)) {
            abort(403, 'You are not allowed to view this request.');
        }

        return view('leave.show', ['leave' => $leave, 'business' => $business]);
    }

    /**
     * Store (create) a leave request.
     */

/**
 * Store a new leave request (simplified policy: only business membership, dates, entitlement, docs).
 */
    public function store(Request $request)
    {
        // 1) Minimal validation to safely fetch LeaveType first
        $base = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
        ]);

        /** @var \App\Models\LeaveType $leaveType */
        $leaveType = LeaveType::findOrFail($base['leave_type_id']);

        // 2) Full validation (attachment rules are permissive; business rules enforced in code below)
        $validated = $request->validate([
            'employee_id'   => 'nullable|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'nullable|string',
            'half_day'      => 'nullable|boolean',
            'half_day_type' => 'nullable|string|in:morning,afternoon|required_if:half_day,1',

            // Files
            'attachment'    => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:2048',
            'attach_later'  => 'nullable|boolean',
        ]);

        return $this->handleTransaction(function () use ($validated, $leaveType, $request) {
            // --- Business context ---
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Active business not found in session.');
            }

            // --- Resolve employee (explicit or current user) ---
            $employeeId = $validated['employee_id'] ?? (auth()->user()->employee->id ?? null);
            if (!$employeeId) {
                return RequestResponse::badRequest('No employee selected for this leave request.');
            }

            /** @var \App\Models\Employee $employee */
            $employee = Employee::with('user')->findOrFail($employeeId);

            // Ensure employee belongs to this business
            if ((int)$employee->business_id !== (int)$business->id) {
                return RequestResponse::badRequest('Selected employee does not belong to the current business.');
            }

            // (Optional) If LeaveType is business-scoped, enforce it (safe even if column doesn’t exist/null)
            if (property_exists($leaveType, 'business_id') && !is_null($leaveType->business_id)) {
                if ((int)$leaveType->business_id !== (int)$business->id) {
                    return RequestResponse::badRequest('This leave type is not available in the current business.');
                }
            }

            // --- Dates & guards ---
            $startDate = Carbon::parse($validated['start_date']);
            $endDate   = Carbon::parse($validated['end_date']);

            if ($startDate->lt(today()) && empty($leaveType->allows_backdating)) {
                return RequestResponse::badRequest('Backdating is not allowed for this leave type.');
            }

            if (!$leaveType->allows_half_day && !empty($validated['half_day'])) {
                return RequestResponse::badRequest('Half-day is not allowed for this leave type.');
            }

            if (is_numeric($leaveType->min_notice_days ?? null)) {
                $diff = now()->startOfDay()->diffInDays($startDate->copy()->startOfDay(), false);
                if ($diff < (int)$leaveType->min_notice_days) {
                    return RequestResponse::badRequest("Minimum notice is {$leaveType->min_notice_days} day(s) before the start date.");
                }
            }

            $totalDays = LeaveRequest::calculateTotalDays(
                $startDate, $endDate, (bool)($validated['half_day'] ?? false), $leaveType
            );

            if (!empty($leaveType->max_continuous_days) && $totalDays > (float)$leaveType->max_continuous_days) {
                return RequestResponse::badRequest("You cannot take more than {$leaveType->max_continuous_days} day(s) for this leave type at once.");
            }

            // --- OVERLAP GUARD (comment this block to disable overlap checking) ---
            //if (LeaveRequest::hasOverlap($employeeId, $startDate, $endDate, null, $business->id)) {
              //  return RequestResponse::badRequest('You already have a pending/approved leave that overlaps with these dates.');
            //}
            // --- END OVERLAP GUARD ---

            // --- Attachment handling ---
            $attachmentPath        = null;
            $requiresDocumentation = false;
            $isTentative           = false;

            if ($leaveType->requires_attachment) {
                $attachLater = (bool)($validated['attach_later'] ?? false);

                if ($request->hasFile('attachment')) {
                    try {
                        $attachmentPath = $request->file('attachment')->store('attachments', 'public');
                    } catch (\Exception $e) {
                        Log::error("Attachment upload failed: ".$e->getMessage());
                        return RequestResponse::badRequest('Failed to upload attachment. Please try again.');
                    }
                } elseif ($attachLater) {
                    $requiresDocumentation = true;
                    if (!empty($leaveType->is_stepwise)) {
                        $isTentative = true; // UI may show "Upload to complete"
                    }
                } else {
                    return RequestResponse::badRequest('Attachment is required for this leave type. You may choose to upload later.');
                }
            } else {
                if ($request->hasFile('attachment')) {
                    try {
                        $attachmentPath = $request->file('attachment')->store('attachments', 'public');
                    } catch (\Exception $e) {
                        Log::error("Attachment upload failed: ".$e->getMessage());
                        return RequestResponse::badRequest('Failed to upload attachment. Please try again.');
                    }
                }
            }

            // --- Entitlement check ---
            $remaining = LeaveEntitlement::where('employee_id', $employeeId)
                ->where('leave_type_id', $leaveType->id)
                ->first()?->getRemainingDays() ?? 0;

            if ($remaining < $totalDays) {
                return RequestResponse::badRequest("You have {$remaining} remaining day(s) for this leave type, but you requested {$totalDays}.");
            }

            // --- Create request ---
            $leaveRequest = LeaveRequest::create([
                'reference_number'       => LeaveRequest::generateUniqueReferenceNumber($business->id),
                'employee_id'            => $employeeId,
                'business_id'            => $business->id,
                'leave_type_id'          => $validated['leave_type_id'],
                'start_date'             => $startDate,
                'end_date'               => $endDate,
                'half_day'               => (bool)($validated['half_day'] ?? false),
                'half_day_type'          => $validated['half_day_type'] ?? null,
                'reason'                 => $validated['reason'] ?? null,
                'attachment'             => $attachmentPath,
                'requires_documentation' => $requiresDocumentation,
                'is_tentative'           => $isTentative,
                'current_approval_level' => 0,
            ]);

            // --- Notifications / kick off approvals ---
            $this->sendApplicationNotifications($leaveRequest);

            return RequestResponse::ok('Leave request created successfully.');
        });
    }


    /**
     * Approve/Reject with level checks.
     */
    public function status(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
            'status'           => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string',
            'comments'         => 'nullable|string|max:500',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            /** @var LeaveRequest $leaveRequest */
            $leaveRequest = LeaveRequest::where('reference_number', $validated['reference_number'])->firstOrFail();

            if (!$leaveRequest->canUserApprove(auth()->user())) {
                return RequestResponse::badRequest('You do not have permission to approve this leave request at this level.');
            }

            if ($validated['status'] === 'approved') {
                return $this->processApproval($leaveRequest, $validated['comments'] ?? null);
            }

            return $this->processRejection($leaveRequest, $validated['rejection_reason'], $validated['comments'] ?? null);
        });
    }

    protected function processApproval(LeaveRequest $leaveRequest, $comments = null)
    {
        $nextLevel      = $leaveRequest->getNextApprovalLevel();
        $requiredLevels = (int) (optional($leaveRequest->leaveType)->approval_levels ?? 1);

        // Require docs before finalizing
        if ($nextLevel >= $requiredLevels) {
            if ($leaveRequest->requires_documentation && !$leaveRequest->attachment) {
                return RequestResponse::badRequest('Cannot finalize approval: documentation is required.');
            }
        }

        // Update level + history (partial approval)
        $leaveRequest->current_approval_level = $nextLevel;

        $history   = $leaveRequest->approval_history ?? [];
        $history[] = [
            'level'         => $nextLevel,
            'approver_id'   => auth()->id(),
            'approver_name' => auth()->user()->name,
            'approved_at'   => now()->toDateTimeString(),
            'comments'      => $comments,
        ];
        $leaveRequest->approval_history = $history;
        $leaveRequest->rejection_reason = null;
        $leaveRequest->save();

        // More approvals needed?
        if ($leaveRequest->needsMoreApprovals()) {
            $this->sendNextLevelNotifications($leaveRequest);

            return RequestResponse::ok("Leave advanced to approval level {$nextLevel}. Waiting for final approval.", [
                'new_status' => 'pending',
            ]);
        }

        // Final approval
        $this->finalizeApproval($leaveRequest);
        $this->sendFinalApprovalNotifications($leaveRequest);

        return RequestResponse::ok('Leave request approved successfully.', [
            'new_status' => 'approved',
        ]);
    }

    protected function processRejection(LeaveRequest $leaveRequest, $rejectionReason, $comments = null)
    {
        $leaveRequest->approved_by            = null;
        $leaveRequest->approved_at            = null;
        $leaveRequest->current_approval_level = 0;
        $leaveRequest->approval_history       = [];
        $leaveRequest->rejection_reason       = $rejectionReason;
        $leaveRequest->is_tentative           = false;
        $leaveRequest->save();

        // Notify employee
        $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));

        return RequestResponse::ok('Leave request rejected successfully.', [
            'new_status' => 'rejected',
        ]);
    }

    protected function sendApplicationNotifications(LeaveRequest $leaveRequest)
    {
        $business  = $leaveRequest->business;
        $employee  = $leaveRequest->employee;
        $leaveType = $leaveRequest->leaveType;

        // Always notify employee of submission
        Mail::to($employee->user->email)->queue(new LeaveRequestSubmitted($leaveRequest));

        $approvalLevels = (int)($leaveType->approval_levels ?? 0);
        if (!$leaveType->requires_approval || $approvalLevels <= 0) {
            if (!$leaveRequest->requires_documentation || $leaveRequest->attachment) {
                $this->finalizeApproval($leaveRequest);
                $this->sendFinalApprovalNotifications($leaveRequest);
            }
            return;
        }

        // Notify ALL HODs in the business
        foreach ($this->findHODApprovers($business) as $hod) {
            Mail::to($hod->email)->queue(new LeaveRequestSubmitted($leaveRequest));
        }

        // Also notify HR on submission (so they get all emails)
        foreach ($this->findBusinessHR($business) as $hr) {
            Mail::to($hr->email)->queue(new LeaveRequestSubmitted($leaveRequest));
        }
    }

    protected function sendNextLevelNotifications(LeaveRequest $leaveRequest)
    {
        $business = $leaveRequest->business;

        // Notify HR
        foreach ($this->findBusinessHR($business) as $hr) {
            Mail::to($hr->email)->queue(new LeaveRequestSubmitted($leaveRequest));
        }

        // Notify ALL HODs too
        foreach ($this->findHODApprovers($business) as $hod) {
            Mail::to($hod->email)->queue(new LeaveRequestSubmitted($leaveRequest));
        }

        // Notify employee of progress
        $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));
    }

    protected function sendFinalApprovalNotifications(LeaveRequest $leaveRequest)
    {
        $business = $leaveRequest->business;

        // Employee
        $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));

        // Admins & Heads
        foreach ($this->findBusinessAdmins($business) as $admin) {
            Mail::to($admin->email)->queue(new LeaveRequestSubmitted($leaveRequest));
        }
        foreach ($this->findBusinessHeads($business) as $head) {
            Mail::to($head->email)->queue(new LeaveRequestSubmitted($leaveRequest));
        }

        // ALL HODs should receive final email too
        foreach ($this->findHODApprovers($business) as $hod) {
            Mail::to($hod->email)->queue(new LeaveRequestSubmitted($leaveRequest));
        }

        // HR too
        foreach ($this->findBusinessHR($business) as $hr) {
            Mail::to($hr->email)->queue(new LeaveRequestSubmitted($leaveRequest));
        }
    }


    /**
     * View permission:
     * - Employee: own only
     * - Others (HOD/HR/Admin/Head): any request in same business

    protected function canUserViewLeaveRequest(User $user, LeaveRequest $leaveRequest)
    {
        $userEmployee = $user->employee;

        if ($user->hasRole('business-employee') && $userEmployee) {
            return (int)$leaveRequest->employee_id === (int)$userEmployee->id;
        }

        // Others must belong to same business
        if ($userEmployee) {
            return (int)$leaveRequest->business_id === (int)$userEmployee->business_id;
        }

        return false;
    }  */

    /**
     * Upload document (owner only).
     */
    public function uploadDocument(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
            'attachment'       => 'required|file|mimes:pdf,jpg,png,doc,docx|max:2048',
        ]);

        $leaveRequest = LeaveRequest::where('reference_number', $validated['reference_number'])->firstOrFail();

        // Only owner can upload
        $authEmployeeId = auth()->user()->employee->id ?? null;
        if (!$authEmployeeId || $authEmployeeId !== (int)$leaveRequest->employee_id) {
            return RequestResponse::badRequest('You are not allowed to upload documents for this leave.');
        }

        try {
            $path = $request->file('attachment')->store('attachments', 'public');
        } catch (\Exception $e) {
            Log::error("Failed to upload attachment for leave {$leaveRequest->id}: ".$e->getMessage());
            return RequestResponse::badRequest('Failed to upload attachment. Please try again.');
        }

        $leaveRequest->attachment             = $path;
        $leaveRequest->requires_documentation = false;
        $leaveRequest->is_tentative           = false;
        $leaveRequest->save();

        // If pending and approvals remain, notify next approvers
        if ($leaveRequest->status === 'pending' && $leaveRequest->needsMoreApprovals()) {
            foreach ($this->findHODApprovers($leaveRequest->business) as $hod) {
                Mail::to($hod->email)->queue(new LeaveRequestSubmitted($leaveRequest));
            }
            foreach ($this->findBusinessHR($leaveRequest->business) as $hr) {
                Mail::to($hr->email)->queue(new LeaveRequestSubmitted($leaveRequest));
            }
        }

        return RequestResponse::ok('Document uploaded successfully. Your request will now proceed for approval.');
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $leaveRequest = LeaveRequest::where('reference_number', $validated['reference_number'])->firstOrFail();

            // Only owner can delete, and only while pending
            if ($leaveRequest->status !== 'pending') {
                return RequestResponse::badRequest('Cannot delete approved or rejected requests.');
            }
            $authEmployeeId = auth()->user()->employee->id ?? null;
            if (!$authEmployeeId || $authEmployeeId !== (int)$leaveRequest->employee_id) {
                return RequestResponse::badRequest('You can only delete your own leave requests.');
            }

            $leaveRequest->delete();
            return RequestResponse::ok('Leave request deleted successfully.');
        });
    }

    /* =========================
     * Helpers
     * ========================= */

    protected function normalizeGender($value): string
    {
        $v = strtolower(trim((string)($value ?? '')));
        if (in_array($v, ['m', 'male']))   return 'male';
        if (in_array($v, ['f', 'female'])) return 'female';
        return 'all';
    }

    /**
     * Finalize approval + entitlement deduction (final level only).
     */
    protected function finalizeApproval(LeaveRequest $leaveRequest): void
    {
        $leaveRequest->approved_by            = auth()->id();
        $leaveRequest->approved_at            = now();
        $leaveRequest->is_tentative           = false;
        $leaveRequest->current_approval_level = max((int)$leaveRequest->current_approval_level, 1);
        $leaveRequest->save();

        $entitlement = LeaveEntitlement::where('employee_id', $leaveRequest->employee_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->first();

        if ($entitlement) {
            if (method_exists($entitlement, 'deductDays')) {
                $entitlement->deductDays((float)$leaveRequest->total_days);
            } elseif (!is_null($entitlement->getAttribute('used_days'))) {
                $entitlement->used_days = (float)($entitlement->used_days ?? 0) + (float)$leaveRequest->total_days;
                $entitlement->save();
            } else {
                Log::warning("Entitlement deduction skipped (no method/field) for entitlement #{$entitlement->id}.");
            }
        } else {
            Log::warning("No entitlement found for employee {$leaveRequest->employee_id} leave_type {$leaveRequest->leave_type_id} when finalizing.");
        }
    }

    /** All HODs for employee's department in this business. */
    protected function findHODApprovers(Business $business)
    {
        return User::role('head-of-department')
            ->whereHas('employee', function ($q) use ($business) {
                $q->where('business_id', $business->id);
            })->get();
    }


    /** All HR users in this business. */
    protected function findBusinessHR(Business $business)
    {
        return User::role('business-hr')
            ->whereHas('employee', function ($q) use ($business) {
                $q->where('business_id', $business->id);
            })->get();
    }

    /** All Business Heads in this business. */
    protected function findBusinessHeads(Business $business)
    {
        return User::role('business-head')
            ->whereHas('employee', function ($q) use ($business) {
                $q->where('business_id', $business->id);
            })->get();
    }

    /** All Business Admins in this business (for final-approval ping). */
    protected function findBusinessAdmins(Business $business)
    {
        return User::role('business-admin')
            ->whereHas('employee', function ($q) use ($business) {
                $q->where('business_id', $business->id);
            })->get();
    }
    protected function canUserViewLeaveRequest(User $user, LeaveRequest $leaveRequest)
    {
        $userEmployee = $user->employee;
        $activeRole   = session('active_role');

        if ($activeRole === 'business-employee' && $userEmployee) {
            return (int)$leaveRequest->employee_id === (int)$userEmployee->id;
        }

        // HOD/HR/Admin/Head: view all requests in the business
        if (in_array($activeRole, ['head-of-department','business-hr','business-admin','business-head'], true) && $userEmployee) {
            return (int)$leaveRequest->business_id === (int)$userEmployee->business_id;
        }

        return false;
    }




// Add this temporary debugging method to your LeaveRequestController.php
// Call this method in a test route to debug the issues

    public function debugLeaveIssues($employeeId, $leaveTypeId, $startDate, $endDate)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $employee = Employee::with('user')->find($employeeId);
        $leaveType = LeaveType::find($leaveTypeId);

        $debugInfo = [
            'employee_info' => [
                'id' => $employee->id ?? 'NOT_FOUND',
                'name' => $employee->user->name ?? 'NOT_FOUND',
                'business_id' => $employee->business_id ?? 'NOT_FOUND',
                'department_id' => $employee->department_id ?? 'NULL',
                'job_category_id' => $employee->job_category_id ?? 'NULL',
                'gender' => $employee->gender ?? $employee->user->gender ?? 'NULL',
            ],
            'business_info' => [
                'id' => $business->id ?? 'NOT_FOUND',
                'slug' => $business->slug ?? 'NOT_FOUND',
            ],
            'leave_type_info' => [
                'id' => $leaveType->id ?? 'NOT_FOUND',
                'name' => $leaveType->name ?? 'NOT_FOUND',
            ],
            'date_info' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'parsed_start' => Carbon::parse($startDate)->toDateString(),
                'parsed_end' => Carbon::parse($endDate)->toDateString(),
            ]
        ];

        // Check existing leave requests for this employee
        $existingRequests = LeaveRequest::where('employee_id', $employeeId)
            ->where('business_id', $business->id)
            ->get(['id', 'reference_number', 'start_date', 'end_date', 'approved_by', 'rejection_reason', 'current_approval_level']);

        $debugInfo['existing_requests'] = $existingRequests->toArray();

        // Check for overlaps with detailed query
        $overlapQuery = LeaveRequest::where('employee_id', $employeeId)
            ->where('business_id', $business->id)
            ->whereNull('rejection_reason') // Only non-rejected
            ->where('start_date', '<=', Carbon::parse($endDate)->toDateString())
            ->where('end_date', '>=', Carbon::parse($startDate)->toDateString());

        $overlappingRequests = $overlapQuery->get(['id', 'reference_number', 'start_date', 'end_date', 'approved_by', 'rejection_reason']);
        $debugInfo['overlapping_requests'] = $overlappingRequests->toArray();
        $debugInfo['has_overlap'] = $overlappingRequests->count() > 0;

        // Check leave policies
        $empGender = $this->normalizeGender($employee->gender ?? $employee->user->gender ?? null);

        $allPolicies = LeavePolicy::where('leave_type_id', $leaveTypeId)->get();
        $debugInfo['all_policies'] = $allPolicies->toArray();

        // Check policy match step by step
        $policyQuery = LeavePolicy::where('leave_type_id', $leaveTypeId);

        if ($employee->department_id) {
            $policyQuery->where(function ($q) use ($employee) {
                $q->where('department_id', $employee->department_id)
                ->orWhereNull('department_id');
            });
        } else {
            $policyQuery->whereNull('department_id');
        }

        if ($employee->job_category_id) {
            $policyQuery->where(function ($q) use ($employee) {
                $q->where('job_category_id', $employee->job_category_id)
                ->orWhereNull('job_category_id');
            });
        } else {
            $policyQuery->whereNull('job_category_id');
        }

        $policyQuery->where(function ($q) use ($empGender) {
            $q->where('gender_applicable', 'all')
            ->orWhere('gender_applicable', $empGender)
            ->orWhereNull('gender_applicable');
        });

        $matchingPolicies = $policyQuery->get();
        $debugInfo['matching_policies'] = $matchingPolicies->toArray();
        $debugInfo['policy_exists'] = $matchingPolicies->count() > 0;
        $debugInfo['normalized_gender'] = $empGender;

        return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
    }

    // Create a test route in your web.php:
    /*
    Route::get('/debug-leave/{employee}/{leaveType}/{startDate}/{endDate}', function($employee, $leaveType, $startDate, $endDate) {
        $controller = new App\Http\Controllers\LeaveRequestController();
        return $controller->debugLeaveIssues($employee, $leaveType, $startDate, $endDate);
    })->middleware(['auth']);
    */


}
