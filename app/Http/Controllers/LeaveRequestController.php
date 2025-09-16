<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
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
     * Optionally fetch leave requests list (for a status tab, e.g., "pending|approved|rejected").
     * Returns a rendered table partial.
     */
    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Active business not found in session.');
        }

        $status = strtolower($request->get('status', 'pending')); // pending|approved|rejected
        $query = LeaveRequest::where('business_id', $business->id)
            ->with(['employee.user', 'leaveType']);

        if (in_array($status, ['pending', 'approved', 'rejected', 'declined'], true)) {
            $query->status($status);
        }

        $leaveRequests = $query->latest('id')->get();
        $currentBusiness = $business; // for blade

        $table = view('leave._leave_requests_table', compact('leaveRequests', 'currentBusiness'))
            ->with('status', $status)
            ->render();

        return RequestResponse::ok('Leave requests fetched successfully.', $table);
    }

    /**
     * Show a single leave request page/partial by reference number.
     */
    public function show(Request $request, $reference = null)
    {
        $ref = $reference ?? $request->get('reference_number');
        if (!$ref) {
            return abort(404, 'Reference number missing.');
        }

        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return abort(404, 'Active business not found.');
        }

        $leave = LeaveRequest::where('business_id', $business->id)
            ->where('reference_number', $ref)
            ->with(['employee.user', 'leaveType', 'approvedBy'])
            ->firstOrFail();

        // Customize your view as needed:
        return view('leave.show', compact('leave', 'business'));
    }

    /**
     * Store a new leave request.
     */
    public function store(Request $request)
    {
        // Validate presence of the leave type first so we can shape the rest of the rules
        $leaveType = LeaveType::findOrFail($request->input('leave_type_id'));

        $rules = [
            'employee_id'    => 'nullable|exists:employees,id',
            'leave_type_id'  => 'required|exists:leave_types,id',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'reason'         => 'nullable|string',
            'half_day'       => 'nullable|boolean',
            'half_day_type'  => 'nullable|string|in:morning,afternoon|required_if:half_day,1',
            // attachment rules below depend on requires_attachment
        ];

        if ($leaveType->requires_attachment) {
            // Accept either attachment now or allow "attach later"
            $rules['attachment']   = 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:2048';
            $rules['attach_later'] = 'nullable|boolean';
        } else {
            $rules['attachment']   = 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:2048';
        }

        $validated = $request->validate($rules);

        return $this->handleTransaction(function () use ($validated, $leaveType, $request) {

            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Active business not found in session.');
            }

            // Respect an explicitly provided employee_id (e.g., HR creating on behalf of someone),
            // otherwise fallback to the authenticated employee.
            $employeeId = $validated['employee_id'] ??
                (auth()->user()->employee->id ?? null);

            if (!$employeeId) {
                return RequestResponse::badRequest('No employee selected for this leave request.');
            }

            $startDate = Carbon::parse($validated['start_date']);
            $endDate   = Carbon::parse($validated['end_date']);

            // Guard: backdating not allowed?
            if ($startDate->lt(today()) && empty($leaveType->allows_backdating)) {
                return RequestResponse::badRequest('Backdating is not allowed for this leave type.');
            }

            // Guard: half-day allowed?
            if (!($leaveType->allows_half_day) && !empty($validated['half_day'])) {
                return RequestResponse::badRequest('Half-day is not allowed for this leave type.');
            }

            // Guard: minimum notice (uses LeaveType min_notice_days)
            if (is_numeric($leaveType->min_notice_days ?? null)) {
                $diff = now()->startOfDay()->diffInDays($startDate->copy()->startOfDay(), false);
                if ($diff < $leaveType->min_notice_days) {
                    return RequestResponse::badRequest("Minimum notice is {$leaveType->min_notice_days} day(s) before the start date.");
                }
            }

            // Calculate total days respecting excluded weekdays, and half-day
            $totalDays = LeaveRequest::calculateTotalDays(
                $startDate,
                $endDate,
                $validated['half_day'] ?? false,
                $leaveType
            );

            // Guard: max continuous days
            if (!empty($leaveType->max_continuous_days) && $totalDays > $leaveType->max_continuous_days) {
                return RequestResponse::badRequest("You cannot take more than {$leaveType->max_continuous_days} day(s) for this leave type at once.");
            }

            // Handle attachments
            $attachmentPath        = null;
            $requiresDocumentation = false;
            $isTentative           = false;

            if ($leaveType->requires_attachment) {
                $attachLater = (bool)($validated['attach_later'] ?? false);

                if ($request->hasFile('attachment')) {
                    try {
                        $attachmentPath = $request->file('attachment')->store('attachments', 'public');
                        Log::info("Attachment uploaded for employee {$employeeId}: {$attachmentPath}");
                    } catch (\Exception $e) {
                        Log::error("Attachment upload failed: ".$e->getMessage());
                        return RequestResponse::badRequest('Failed to upload attachment. Please try again.');
                    }
                } elseif ($attachLater) {
                    $requiresDocumentation = true;
                    // If stepwise flow is enabled, the request can be tentative until doc arrives
                    if ($leaveType->is_stepwise) {
                        $isTentative = true;
                    }
                } else {
                    return RequestResponse::badRequest('Attachment is required for this leave type. You may choose to upload later.');
                }
            } else {
                // Not required; if provided, store it
                if ($request->hasFile('attachment')) {
                    try {
                        $attachmentPath = $request->file('attachment')->store('attachments', 'public');
                    } catch (\Exception $e) {
                        Log::error("Attachment upload failed: ".$e->getMessage());
                        return RequestResponse::badRequest('Failed to upload attachment. Please try again.');
                    }
                }
            }

            // Overlap guard (pending/approved/tentative)
            if (LeaveRequest::hasOverlap($employeeId, $startDate, $endDate)) {
                return RequestResponse::badRequest('You already have a leave request that overlaps with these dates.');
            }

            // Entitlement guard (if model available)
            $remaining = \App\Models\LeaveEntitlement::where('employee_id', $employeeId)
                ->where('leave_type_id', $leaveType->id)
                ->first()?->getRemainingDays() ?? 0;

            if ($remaining < $totalDays) {
                return RequestResponse::badRequest("You have {$remaining} remaining day(s) for this leave type, but you requested {$totalDays}.");
            }

            // Create the leave request
            $leaveRequest = LeaveRequest::create([
                'reference_number'       => LeaveRequest::generateUniqueReferenceNumber($business->id),
                'employee_id'            => $employeeId,
                'business_id'            => $business->id,
                'leave_type_id'          => $validated['leave_type_id'],
                'start_date'             => $startDate,
                'end_date'               => $endDate,
                'half_day'               => $validated['half_day'] ?? false,
                'half_day_type'          => $validated['half_day_type'] ?? null,
                'reason'                 => $validated['reason'] ?? null,
                'attachment'             => $attachmentPath,
                'requires_documentation' => $requiresDocumentation,
                'is_tentative'           => $isTentative,
                'current_approval_level' => 0,
                // approval_history starts as null/[]
            ]);

            // Auto-approval logic for simple flows
            if (!$leaveType->requires_approval || (int)$leaveType->approval_levels <= 0) {
                // No approvals required at all -> finalize if docs OK
                if ($leaveType->requires_attachment && $leaveRequest->requires_documentation && !$leaveRequest->attachment) {
                    $leaveRequest->is_tentative = true;
                    $leaveRequest->save();
                } else {
                    // finalize
                    $leaveRequest->approved_by = auth()->id();
                    $leaveRequest->approved_at = now();
                    $leaveRequest->current_approval_level = max(1, (int)$leaveType->approval_levels);
                    $leaveRequest->save();
                }
            } elseif ((int)$leaveType->approval_levels === 1) {
                // Single-level approval required
                if ($leaveType->requires_attachment && $leaveRequest->requires_documentation && !$leaveRequest->attachment) {
                    // let it remain tentative; HR will finalize after doc
                    $leaveRequest->is_tentative = true;
                    $leaveRequest->save();
                }
                // otherwise wait for explicit approval through status() endpoint
            }

            // Notify HR/admin if configured
            if ($business->hr_email) {
                Log::info('Sending leave request email to HR: '.$business->hr_email);
                Mail::to($business->hr_email)->queue(new LeaveRequestSubmitted($leaveRequest));
            } else {
                Log::warning("Leave request {$leaveRequest->id} submitted but no HR email on business {$business->id}");
            }

            return RequestResponse::ok('Leave request created successfully.');
        });
    }

    /**
     * Approve or reject a request.
     * - Approve increments approval level; finalizes when last level reached.
     * - Reject clears approvals and sets a rejection reason.
     */
    public function status(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
            'status'           => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|required_if:status,rejected|string',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $leaveRequest = LeaveRequest::where('reference_number', $validated['reference_number'])->firstOrFail();
            $leaveType    = $leaveRequest->leaveType;

            if ($validated['status'] === 'approved') {
                // Increment approval level
                $current = (int)($leaveRequest->current_approval_level ?? 0);
                $next    = $current + 1;
                $leaveRequest->current_approval_level = $next;

                // Push to approval history
                $history   = $leaveRequest->approval_history ?? [];
                $history[] = [
                    'level'       => $next,
                    'approver_id' => auth()->id(),
                    'approved_at' => now()->toDateTimeString(),
                ];
                $leaveRequest->approval_history = $history;

                // Partial approval (not yet at final level)
                if ($leaveType && (int)$leaveType->approval_levels > 0 && $next < (int)$leaveType->approval_levels) {
                    $leaveRequest->rejection_reason = null;
                    $leaveRequest->save();

                    // notify employee of progress
                    $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));

                    return RequestResponse::ok("Leave advanced to approval level {$next}.");
                }

                // Final approval: check doc presence if required
                if ($leaveType && $leaveType->requires_attachment && $leaveRequest->requires_documentation && !$leaveRequest->attachment) {
                    return RequestResponse::badRequest('Cannot finalize approval: documentation is required. Ask the employee to upload and retry.');
                }

                // Finalize
                $leaveRequest->approved_by      = auth()->id();
                $leaveRequest->approved_at      = now();
                $leaveRequest->rejection_reason = null;
                $leaveRequest->is_tentative     = false;
                $leaveRequest->save();

                // notify employee
                $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));

                return RequestResponse::ok('Leave request approved successfully.');
            }

            // Rejection path
            $leaveRequest->approved_by            = null;
            $leaveRequest->approved_at            = null;
            $leaveRequest->current_approval_level = 0;
            $leaveRequest->approval_history       = [];
            $leaveRequest->rejection_reason       = $validated['rejection_reason'];
            $leaveRequest->is_tentative           = false;
            $leaveRequest->save();

            $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));

            return RequestResponse::ok('Leave request rejected successfully.');
        });
    }

    /**
     * Employee uploads a document later for an existing request.
     */
    public function uploadDocument(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
            'attachment'       => 'required|file|mimes:pdf,jpg,png,doc,docx|max:2048',
        ]);

        $leaveRequest = LeaveRequest::where('reference_number', $validated['reference_number'])->firstOrFail();

        // Only the owner can upload
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

        // HR/Approver should finalize via the status() endpoint when ready.
        $business = $leaveRequest->business;
        if ($business && $business->hr_email) {
            Mail::to($business->hr_email)->queue(new LeaveRequestSubmitted($leaveRequest));
        }

        return RequestResponse::ok('Document uploaded successfully.');
    }

    /**
     * Delete a leave request.
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $leaveRequest = LeaveRequest::where('reference_number', $validated['reference_number'])->firstOrFail();
            $leaveRequest->delete();

            return RequestResponse::ok('Leave request deleted successfully.');
        });
    }
}
