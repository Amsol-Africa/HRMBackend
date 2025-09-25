<?php namespace App\Http\Controllers;

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
     * - Employees: only their own (by ACTIVE role)
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

        // Scope by ACTIVE role (not just hasRole)
        $user = auth()->user();
        $emp = $user->employee;
        $activeRole = session('active_role');

        if ($activeRole === 'business-employee' && $emp) {
            $query->where('employee_id', $emp->id);
        }

        // Filter by tab status
        if (in_array($status, ['pending', 'approved', 'rejected', 'declined'], true)) {
            $query->status($status);
        }

        $leaveRequests = $query->latest('id')->get();
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
            'employee_id' => 'nullable|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'half_day' => 'nullable|boolean',
            'half_day_type' => 'nullable|string|in:morning,afternoon|required_if:half_day,1',
            // Files
            'attachment' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:2048',
            'attach_later' => 'nullable|boolean',
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

            // (Optional) If LeaveType is business-scoped, enforce it (safe even if column doesn't exist/null)
            if (property_exists($leaveType, 'business_id') && !is_null($leaveType->business_id)) {
                if ((int)$leaveType->business_id !== (int)$business->id) {
                    return RequestResponse::badRequest('This leave type is not available in the current business.');
                }
            }

            // --- Dates & guards ---
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);

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
                $startDate,
                $endDate,
                (bool)($validated['half_day'] ?? false),
                $leaveType
            );

            if (!empty($leaveType->max_continuous_days) && $totalDays > (float)$leaveType->max_continuous_days) {
                return RequestResponse::badRequest("You cannot take more than {$leaveType->max_continuous_days} day(s) for this leave type at once.");
            }

            // --- OVERLAP GUARD ---
            if (LeaveRequest::hasOverlap($employeeId, $startDate, $endDate)) {
                return RequestResponse::badRequest('You already have a pending/approved leave that overlaps with these dates.');
            }
            // --- END OVERLAP GUARD ---

            // --- Attachment handling ---
            $attachmentPath = null;
            $requiresDocumentation = false;
            $isTentative = false;

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
                'reference_number' => LeaveRequest::generateUniqueReferenceNumber($business->id),
                'employee_id' => $employeeId,
                'business_id' => $business->id,
                'leave_type_id' => $validated['leave_type_id'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'half_day' => (bool)($validated['half_day'] ?? false),
                'half_day_type' => $validated['half_day_type'] ?? null,
                'reason' => $validated['reason'] ?? null,
                'attachment' => $attachmentPath,
                'requires_documentation' => $requiresDocumentation,
                'is_tentative' => $isTentative,
                'current_approval_level' => 0,
            ]);

            // --- Handle approval process based on leave type settings ---
            $this->handleLeaveApprovalProcess($leaveRequest);

            return RequestResponse::ok('Leave request created successfully.');
        });
    }

    /**
     * Handle the approval process for a newly created leave request
     */
    protected function handleLeaveApprovalProcess(LeaveRequest $leaveRequest)
    {
        $leaveType = $leaveRequest->leaveType;
        $approvalLevels = (int)($leaveType->approval_levels ?? 1);

        // Check if this leave type requires approval
        $requiresApproval = $leaveType->requires_approval ?? true;

        // If leave type doesn't require approval, auto-approve it
        if (!$requiresApproval) {
            // Only auto-approve if documentation is not required OR documentation is already provided
            if (!$leaveRequest->requires_documentation || $leaveRequest->attachment) {
                $this->autoApproveLeave($leaveRequest);
                return;
            }
            // If documentation is required but not provided, leave it pending until document is uploaded
        }

        // If approval is required, send notifications to approvers
        // Leave will remain in pending status until someone approves it
        $this->sendApplicationNotifications($leaveRequest);
    }

    /**
     * Auto-approve a leave request (for leave types that don't require approval)
     */
    protected function autoApproveLeave(LeaveRequest $leaveRequest)
    {
        try {
            // Set the system as the approver for auto-approved leaves
            $leaveRequest->approved_by = auth()->id() ?? 1; // Use current user or system user
            $leaveRequest->approved_at = now();
            $leaveRequest->is_tentative = false;
            $leaveRequest->current_approval_level = 1;

            // Add to approval history
            $history = [];
            $history[] = [
                'level' => 1,
                'approver_id' => $leaveRequest->approved_by,
                'approver_name' => 'System Auto-Approval',
                'approved_at' => now()->toDateTimeString(),
                'comments' => 'Auto-approved (no approval required for this leave type)',
            ];
            $leaveRequest->approval_history = $history;
            $leaveRequest->save();

            // Handle entitlement deduction
            $this->deductLeaveEntitlementSafely($leaveRequest);

            // Send auto-approval notifications
            $this->sendFinalApprovalNotificationsWithDelay($leaveRequest);

        } catch (\Exception $e) {
            Log::error("Error auto-approving leave {$leaveRequest->id}: " . $e->getMessage());
        }
    }

    /**
     * Approve/Reject with level checks.
     */
    public function status(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string',
            'comments' => 'nullable|string|max:500',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            try {
                /** @var LeaveRequest $leaveRequest */
                $leaveRequest = LeaveRequest::where('reference_number', $validated['reference_number'])->firstOrFail();

                if (!$leaveRequest->canUserApprove(auth()->user())) {
                    return RequestResponse::badRequest('You do not have permission to approve this leave request at this level.');
                }

                if ($leaveRequest->status !== 'pending') {
                    return RequestResponse::badRequest('This leave request has already been processed.');
                }

                if ($validated['status'] === 'approved') {
                    return $this->processApproval($leaveRequest, $validated['comments'] ?? null);
                }

                return $this->processRejection($leaveRequest, $validated['rejection_reason'], $validated['comments'] ?? null);
            } catch (\Exception $e) {
                Log::error("Error in leave status method: " . $e->getMessage());
                return RequestResponse::badRequest('Failed to process leave request. Please try again.');
            }
        });
    }

    /**
     * Enhanced processApproval with better error handling
     */
    protected function processApproval(LeaveRequest $leaveRequest, $comments = null)
    {
        try {
            $nextLevel = $leaveRequest->getNextApprovalLevel();
            $requiredLevels = (int) (optional($leaveRequest->leaveType)->approval_levels ?? 1);

            // Validate approver
            $approverId = auth()->id();
            if (!$approverId) {
                return RequestResponse::badRequest('Invalid approver session.');
            }

            // Require docs before finalizing
            if ($nextLevel >= $requiredLevels) {
                if ($leaveRequest->requires_documentation && !$leaveRequest->attachment) {
                    return RequestResponse::badRequest('Cannot finalize approval: documentation is required.');
                }
            }

            // Update level + history (partial approval)
            $leaveRequest->current_approval_level = $nextLevel;
            $history = $leaveRequest->approval_history ?? [];
            $history[] = [
                'level' => $nextLevel,
                'approver_id' => $approverId,
                'approver_name' => auth()->user()->name,
                'approved_at' => now()->toDateTimeString(),
                'comments' => $comments,
            ];
            $leaveRequest->approval_history = $history;
            $leaveRequest->rejection_reason = null;
            $leaveRequest->save();

            // More approvals needed?
            if ($leaveRequest->needsMoreApprovals()) {
                $this->sendNextLevelNotificationsWithDelay($leaveRequest);
                return RequestResponse::ok("Leave advanced to approval level {$nextLevel}. Waiting for final approval.", [
                    'new_status' => 'pending',
                ]);
            }

            // Final approval
            $this->finalizeApprovalSafely($leaveRequest);
            $this->sendFinalApprovalNotificationsWithDelay($leaveRequest);

            return RequestResponse::ok('Leave request approved successfully.', [
                'new_status' => 'approved',
            ]);
        } catch (\Exception $e) {
            Log::error("Error processing leave approval for {$leaveRequest->reference_number}: " . $e->getMessage());
            return RequestResponse::badRequest('Failed to process approval. Please try again.');
        }
    }

    protected function processRejection(LeaveRequest $leaveRequest, $rejectionReason, $comments = null)
    {
        $leaveRequest->approved_by = null;
        $leaveRequest->approved_at = null;
        $leaveRequest->current_approval_level = 0;
        $leaveRequest->approval_history = [];
        $leaveRequest->rejection_reason = $rejectionReason;
        $leaveRequest->is_tentative = false;
        $leaveRequest->save();

        // Notify employee
        $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));

        return RequestResponse::ok('Leave request rejected successfully.', [
            'new_status' => 'rejected',
        ]);
    }

    /**
     * Send application notifications with delays to prevent rate limiting
     * Modified to NOT auto-approve leaves that require approval
     */
    protected function sendApplicationNotifications(LeaveRequest $leaveRequest)
    {
        try {
            $business = $leaveRequest->business;
            $employee = $leaveRequest->employee;
            $leaveType = $leaveRequest->leaveType;

            // Always notify employee of submission (immediate)
            Mail::to($employee->user->email)->queue(new LeaveRequestSubmitted($leaveRequest));

            $approvalLevels = (int)($leaveType->approval_levels ?? 1);

            // If approval is required, notify approvers
            // The leave will remain in pending status until manually approved
            if ($leaveType->requires_approval && $approvalLevels > 0) {
                // Collect all recipients and send with delays
                $recipients = collect();
                $recipients = $recipients->merge($this->findHODApprovers($business)->pluck('email'));
                $recipients = $recipients->merge($this->findBusinessHR($business)->pluck('email'));

                // Send emails with 5-second delays
                foreach ($recipients->unique() as $index => $email) {
                    $delay = now()->addSeconds(($index + 1) * 5);
                    Mail::to($email)->later($delay, new LeaveRequestSubmitted($leaveRequest));
                }
            }

            // Note: We removed the auto-approval logic that was causing the issue
            // Leaves that require approval will stay pending until someone approves them

        } catch (\Exception $e) {
            Log::error("Error sending application notifications for {$leaveRequest->reference_number}: " . $e->getMessage());
        }
    }

    /**
     * Send next level notifications with delays
     */
    protected function sendNextLevelNotificationsWithDelay(LeaveRequest $leaveRequest)
    {
        try {
            $business = $leaveRequest->business;

            // Collect all recipients
            $recipients = collect();
            $recipients = $recipients->merge($this->findBusinessHR($business)->pluck('email'));
            $recipients = $recipients->merge($this->findHODApprovers($business)->pluck('email'));

            // Send with 5-second delays between each email
            foreach ($recipients->unique() as $index => $email) {
                $delay = now()->addSeconds(($index + 1) * 5);
                Mail::to($email)->later($delay, new LeaveRequestSubmitted($leaveRequest));
            }

            // Notify employee of progress (immediate)
            $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));
        } catch (\Exception $e) {
            Log::error("Error sending next level notifications for {$leaveRequest->reference_number}: " . $e->getMessage());
        }
    }

    /**
     * Send final approval notifications with delays to prevent rate limiting
     */
    protected function sendFinalApprovalNotificationsWithDelay(LeaveRequest $leaveRequest)
    {
        try {
            $business = $leaveRequest->business;

            // Employee notification (highest priority - immediate)
            $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));

            // Collect all other recipients
            $recipients = collect();
            $recipients = $recipients->merge($this->findBusinessAdmins($business)->pluck('email'));
            $recipients = $recipients->merge($this->findBusinessHeads($business)->pluck('email'));
            $recipients = $recipients->merge($this->findHODApprovers($business)->pluck('email'));
            $recipients = $recipients->merge($this->findBusinessHR($business)->pluck('email'));

            // Send emails with 10-second delays to avoid rate limiting
            foreach ($recipients->unique() as $index => $email) {
                $delay = now()->addSeconds(($index + 1) * 10);
                Mail::to($email)->later($delay, new LeaveRequestSubmitted($leaveRequest));
            }
        } catch (\Exception $e) {
            Log::error("Error sending final approval notifications for {$leaveRequest->reference_number}: " . $e->getMessage());
        }
    }

    /**
     * Enhanced finalizeApproval with better error handling
     */
    protected function finalizeApprovalSafely(LeaveRequest $leaveRequest): void
    {
        try {
            $approverId = auth()->id();
            if (!$approverId) {
                throw new \Exception('No authenticated user found for approval');
            }

            $leaveRequest->approved_by = $approverId;
            $leaveRequest->approved_at = now();
            $leaveRequest->is_tentative = false;
            $leaveRequest->current_approval_level = max((int)$leaveRequest->current_approval_level, 1);
            $leaveRequest->save();

            // Handle entitlement deduction with better error handling
            $this->deductLeaveEntitlementSafely($leaveRequest);
        } catch (\Exception $e) {
            Log::error("Error finalizing approval for leave {$leaveRequest->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Safe entitlement deduction with better error handling
     */
    protected function deductLeaveEntitlementSafely(LeaveRequest $leaveRequest): void
    {
        try {
            $entitlement = LeaveEntitlement::where('employee_id', $leaveRequest->employee_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->first();

            if (!$entitlement) {
                Log::warning("No entitlement found for employee {$leaveRequest->employee_id} leave_type {$leaveRequest->leave_type_id} when finalizing.");
                return;
            }

            if (method_exists($entitlement, 'deductDays')) {
                $entitlement->deductDays((float)$leaveRequest->total_days);
            } elseif (!is_null($entitlement->getAttribute('used_days'))) {
                $entitlement->used_days = (float)($entitlement->used_days ?? 0) + (float)$leaveRequest->total_days;
                $entitlement->save();
            } else {
                // Use the getRemainingDays method which recalculates and saves
                $entitlement->getRemainingDays();
                Log::info("Entitlement updated using getRemainingDays for entitlement #{$entitlement->id}.");
            }
        } catch (\Exception $e) {
            Log::error("Error deducting leave entitlement for leave {$leaveRequest->id}: " . $e->getMessage());
            // Don't throw here - entitlement issues shouldn't block approval
        }
    }

    /**
     * Replace the old finalizeApproval method with this call
     */
    protected function finalizeApproval(LeaveRequest $leaveRequest): void
    {
        $this->finalizeApprovalSafely($leaveRequest);
    }

    /**
     * Replace the old notification methods with delay versions
     */
    protected function sendNextLevelNotifications(LeaveRequest $leaveRequest)
    {
        $this->sendNextLevelNotificationsWithDelay($leaveRequest);
    }

    protected function sendFinalApprovalNotifications(LeaveRequest $leaveRequest)
    {
        $this->sendFinalApprovalNotificationsWithDelay($leaveRequest);
    }

    /* =========================
     * Other existing helpers
     * =========================
     */

    /**
     * View permission:
     * - Employee: own only
     * - Others (HOD/HR/Admin/Head): any request in same business
     */
    protected function canUserViewLeaveRequest(User $user, LeaveRequest $leaveRequest)
    {
        $userEmployee = $user->employee;
        $activeRole = session('active_role');

        if ($activeRole === 'business-employee' && $userEmployee) {
            return (int)$leaveRequest->employee_id === (int)$userEmployee->id;
        }

        // HOD/HR/Admin/Head: view all requests in the business
        if (in_array($activeRole, ['head-of-department','business-hr','business-admin','business-head'], true) && $userEmployee) {
            return (int)$leaveRequest->business_id === (int)$userEmployee->business_id;
        }

        return false;
    }

    /**
     * Upload document (owner only).
     */
    public function uploadDocument(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
            'attachment' => 'required|file|mimes:pdf,jpg,png,doc,docx|max:2048',
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

        $leaveRequest->attachment = $path;
        $leaveRequest->requires_documentation = false;
        $leaveRequest->is_tentative = false;
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
     * Finders
     * =========================
     */

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

    // --------------------------
    // Debug helper (unchanged)
    // --------------------------
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

    /**
     * Helper method to normalize gender values
     */
    protected function normalizeGender($gender)
    {
        if (!$gender) return 'all';

        $gender = strtolower(trim($gender));

        if (in_array($gender, ['male', 'm'])) {
            return 'male';
        } elseif (in_array($gender, ['female', 'f'])) {
            return 'female';
        }

        return 'all';
    }
}
