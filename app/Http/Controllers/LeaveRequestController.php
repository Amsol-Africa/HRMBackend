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
use App\Mail\LeaveRequestSubmitted;
use App\Notifications\LeaveStatusNotification;

class LeaveRequestController extends Controller
{
    use HandleTransactions;

    // ... fetch(), show() unchanged ...

    public function store(Request $request)
    {
        $leaveType = LeaveType::findOrFail($request->leave_type_id);

        // Validation rules
        $rules = [
            'employee_id'   => 'nullable|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'nullable|string',
            'half_day'      => 'nullable|boolean',
            'half_day_type' => 'nullable|string|in:morning,afternoon',
            'attach_later'  => 'nullable|boolean', // new - if true, user will upload doc later
        ];

        // If the leave type requires attachment, allow attach_later option (employee can choose)
        if ($leaveType->requires_attachment) {
            // we will accept either attachment OR attach_later
            $rules['attachment'] = 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:2048';
            $rules['attach_later'] = 'nullable|boolean';
        } else {
            $rules['attachment'] = 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:2048';
        }

        $validatedData = $request->validate($rules);

        return $this->handleTransaction(function () use ($validatedData, $leaveType, $request) {
            $business   = Business::findBySlug(session('active_business_slug'));
            $employeeId = auth()->user()->employee->id;

            $startDate  = Carbon::parse($validatedData['start_date']);
            $endDate    = Carbon::parse($validatedData['end_date']);

            // Backdating rule
            if ($startDate->lt(today()) && !$leaveType->allows_backdating) {
                return RequestResponse::badRequest('Backdating is not allowed for this leave type.');
            }

            // Calculate total days using LeaveType excluded days
            $totalDays  = LeaveRequest::calculateTotalDays(
                $startDate,
                $endDate,
                $validatedData['half_day'] ?? false,
                $leaveType
            );

            // Check max continuous days
            if ($leaveType->max_continuous_days && $totalDays > $leaveType->max_continuous_days) {
                return RequestResponse::badRequest("You cannot take more than {$leaveType->max_continuous_days} days for this leave type.");
            }

            // Handle attachment vs attach_later
            $attachmentPath = null;
            $requiresDocumentation = false;
            $isTentative = false;

            if ($leaveType->requires_attachment) {
                $attachLater = $validatedData['attach_later'] ?? false;
                if ($request->hasFile('attachment')) {
                    try {
                        $attachmentPath = $request->file('attachment')->store('attachments', 'public');
                        \Log::info("Attachment uploaded successfully for employee {$employeeId}: {$attachmentPath}");
                    } catch (\Exception $e) {
                        \Log::error("Failed to upload attachment: " . $e->getMessage());
                        return RequestResponse::badRequest('Failed to upload attachment. Please try again.');
                    }
                } elseif ($attachLater) {
                    // user chose to upload later
                    $requiresDocumentation = true;
                    // If leave type is stepwise, allow the tentative submission (employee will not be fully approved yet)
                    if ($leaveType->is_stepwise) {
                        $isTentative = true;
                    }
                } else {
                    // no file & not attach_later -> reject (because leaveType requires attachment)
                    return RequestResponse::badRequest('Attachment is required for this leave type. You can also choose to upload later.');
                }
            } else {
                // not required - if provided store
                if ($request->hasFile('attachment')) {
                    try {
                        $attachmentPath = $request->file('attachment')->store('attachments', 'public');
                    } catch (\Exception $e) {
                        \Log::error("Failed to upload attachment: " . $e->getMessage());
                        return RequestResponse::badRequest('Failed to upload attachment. Please try again.');
                    }
                }
            }

            // Check for overlapping leaves (only approved + pending + tentative) - keep old behavior
            if (LeaveRequest::hasOverlap($employeeId, $startDate, $endDate)) {
                return RequestResponse::badRequest('You already have a leave request that overlaps with these dates.');
            }

            // Create the leave request
            $leaveRequest = LeaveRequest::create([
                'reference_number' => LeaveRequest::generateUniqueReferenceNumber($business->id),
                'employee_id'      => $employeeId,
                'business_id'      => $business->id,
                'leave_type_id'    => $validatedData['leave_type_id'],
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'half_day'         => $validatedData['half_day'] ?? false,
                'half_day_type'    => $validatedData['half_day_type'] ?? null,
                'reason'           => $validatedData['reason'] ?? null,
                'attachment'       => $attachmentPath,
                'requires_documentation' => $requiresDocumentation,
                'is_tentative' => $isTentative,
                'current_approval_level' => ($leaveType->approval_levels > 0 && !$isTentative) ? 0 : 0,
                // approval_history left null initially
            ]);

            // If the leave type does not require approval, or approval_levels == 0 (treat as auto-approved),
            // we can auto-approve (but only final approval counts to deduction)
            if (!$leaveType->requires_approval || $leaveType->approval_levels <= 1) {
                // For approval_levels == 1 we still need to check document requirement:
                if ($leaveType->requires_attachment && $leaveRequest->requires_documentation && !$leaveRequest->attachment) {
                    // can't finalize: still requires document; leave is tentative
                    $leaveRequest->is_tentative = true;
                    $leaveRequest->save();
                } else {
                    // finalize approval
                    $leaveRequest->approved_by = auth()->id();
                    $leaveRequest->approved_at = now();
                    $leaveRequest->save();
                }
            }

            // Notify HR/admin if configured
            if ($business->hr_email) {
                \Log::info('Sending leave request email to HR: ' . $business->hr_email);
                Mail::to($business->hr_email)->queue(new LeaveRequestSubmitted($leaveRequest));
            } else {
                \Log::warning("Leave request {$leaveRequest->id} submitted but no HR email found for business ID {$business->id}");
            }

            return RequestResponse::ok('Leave request created successfully.');
        });
    }

    /**
     * Approval / rejection endpoint
     * Accepts 'status' => 'approved'|'rejected'
     * For approvals, it will increment approval level (if >1) and finalize only when last level is reached.
     */
    public function status(Request $request)
    {
        $validatedData = $request->validate([
            'reference_number'  => 'required|exists:leave_requests,reference_number',
            'status'            => 'required|in:approved,rejected',
            'rejection_reason'  => 'nullable|required_if:status,rejected|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $leaveRequest = LeaveRequest::where('reference_number', $validatedData['reference_number'])->firstOrFail();
            $leaveType = $leaveRequest->leaveType;

            if ($validatedData['status'] === 'approved') {
                // increment approval level
                $current = $leaveRequest->current_approval_level ?? 0;
                $next = $current + 1;
                $leaveRequest->current_approval_level = $next;

                // push to approval_history
                $history = $leaveRequest->approval_history ?? [];
                $history[] = [
                    'level' => $next,
                    'approver_id' => auth()->id(),
                    'approved_at' => now()->toDateTimeString(),
                ];
                $leaveRequest->approval_history = $history;

                // if not yet final (approval_levels > next) -> partial approval
                if ($leaveType && $leaveType->approval_levels && $next < $leaveType->approval_levels) {
                    // partial approval - keep approved_by null (final approval not yet reached)
                    $leaveRequest->rejection_reason = null;
                    $leaveRequest->save();

                    // notify employee that leave has moved one approval level
                    $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));

                    return RequestResponse::ok("Leave advanced to approval level {$next}.");
                }

                // final approval: ensure documentation presence if required
                if ($leaveType && $leaveType->requires_attachment && $leaveRequest->requires_documentation && !$leaveRequest->attachment) {
                    return RequestResponse::badRequest('Cannot finalize approval: documentation (attachment) is required. Ask the employee to upload it and retry final approval.');
                }

                // finalize approval: set approved_by and approved_at
                $leaveRequest->approved_by = auth()->id();
                $leaveRequest->approved_at = now();
                $leaveRequest->rejection_reason = null;
                $leaveRequest->save();

                // notify employee
                $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));

                return RequestResponse::ok('Leave request approved successfully.');
            } else {
                // rejection: clear approvals, set rejection_reason
                $leaveRequest->approved_by = null;
                $leaveRequest->approved_at = null;
                $leaveRequest->current_approval_level = 0;
                $leaveRequest->approval_history = [];
                $leaveRequest->rejection_reason = $validatedData['rejection_reason'];
                $leaveRequest->save();

                $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));
                return RequestResponse::ok('Leave request rejected successfully.');
            }
        });
    }

    /**
     * Upload document later (employee action). If a document is uploaded and it's a stepwise flow,
     * optionally the system can attempt to auto-finalize (if approval_levels == 1) or notify HR.
     */
    public function uploadDocument(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
            'attachment' => 'required|file|mimes:pdf,jpg,png,doc,docx|max:2048',
        ]);

        $leaveRequest = LeaveRequest::where('reference_number', $validated['reference_number'])->firstOrFail();

        // only the owner can upload
        if (auth()->user()->employee->id !== $leaveRequest->employee_id) {
            return RequestResponse::badRequest('You are not allowed to upload documents for this leave.');
        }

        try {
            $path = $request->file('attachment')->store('attachments', 'public');
        } catch (\Exception $e) {
            \Log::error("Failed to upload attachment for leave {$leaveRequest->id}: ".$e->getMessage());
            return RequestResponse::badRequest('Failed to upload attachment. Please try again.');
        }

        $leaveRequest->attachment = $path;
        $leaveRequest->requires_documentation = false;
        $leaveRequest->is_tentative = false;
        $leaveRequest->save();

        // If leave type requires approval_levels == 1 and requires_approval=true, we could optionally auto-finalize:
        $leaveType = $leaveRequest->leaveType;
        if ($leaveType && $leaveType->requires_approval && $leaveType->approval_levels <= 1) {
            // auto-finalize not done automatically here; prefer HR to finalize.
            // but you can auto-finalize if you want:
            $leaveRequest->approved_by = auth()->id(); $leaveRequest->approved_at = now(); $leaveRequest->save();
        }

        // Notify HR/admin that doc has been uploaded
        $business = $leaveRequest->business;
        if ($business && $business->hr_email) {
            Mail::to($business->hr_email)->queue(new LeaveRequestSubmitted($leaveRequest));
        }

        return RequestResponse::ok('Document uploaded successfully.');
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $leaveRequest = LeaveRequest::where('reference_number', $validatedData['reference_number'])->firstOrFail();
            $leaveRequest->delete();
            return RequestResponse::ok('Leave request deleted successfully.');
        });
    }
}
