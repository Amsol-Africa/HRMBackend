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

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $leaveRequests = LeaveRequest::where('business_id', $business->id);
        $status = $request->status;

        $user = auth()->user();
        $activeRole = session('active_role');

        // Restrict non-HR/admin users to their own leaves
        if (!in_array($activeRole, ['business-hr', 'business-admin','head-of-department'])) {
            if ($user->employee) {
                $leaveRequests->where('employee_id', $user->employee->id);
            } else {
                $leaveRequests->whereRaw('1=0'); // no results
            }
        }

        // Status filtering
        if ($status) {
            $leaveRequests->status($status);
        }

        $leaveRequests = $leaveRequests
            ->with('employee', 'leaveType')
            ->latest()
            ->paginate(10);

        $leaveRequestsTable = view('leave._leave_requests_table', compact('leaveRequests', 'status'))->render();
        return RequestResponse::ok('Leave requests fetched successfully.', $leaveRequestsTable);
    }

    public function show(Request $request, $referenceNumber)
    {
        $leaveRequest = LeaveRequest::where('reference_number', $referenceNumber)
            ->with('employee', 'leaveType')
            ->firstOrFail();

        $leaveRequestDetails = view('leave._leave_request_table', compact('leaveRequest'))->render();
        return RequestResponse::ok('Leave request fetched successfully.', $leaveRequestDetails);
    }

    public function store(Request $request)
    {
        $leaveType = LeaveType::findOrFail($request->leave_type_id);

        $rules = [
            'employee_id'   => 'nullable|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'nullable|string',
        ];

        if ($leaveType->requires_attachment) {
            $rules['attachment'] = 'required|file|mimes:pdf,jpg,png,doc,docx|max:2048';
        }

        $validatedData = $request->validate($rules);

        return $this->handleTransaction(function () use ($validatedData, $leaveType, $request) {
            $business   = Business::findBySlug(session('active_business_slug'));
            $employeeId = auth()->user()->employee->id;

            $startDate  = Carbon::parse($validatedData['start_date']);
            $endDate    = Carbon::parse($validatedData['end_date']);
            $totalDays  = $endDate->diffInDays($startDate) + 1;

            if ($startDate->lt(today()) || $endDate->lt(today())) {
                return RequestResponse::badRequest('You cannot apply for leave in the past.');
            }

            if ($leaveType->max_continuous_days && $totalDays > $leaveType->max_continuous_days) {
                return RequestResponse::badRequest("You cannot take more than {$leaveType->max_continuous_days} days for this leave type.");
            }

            // ✅ Unified overlap check (pending + approved, not rejected)
            if (LeaveRequest::hasOverlap($employeeId, $startDate, $endDate)) {
                return RequestResponse::badRequest('You already have a leave request that overlaps with these dates.');
            }

            // Create leave request
            $leaveRequest = new LeaveRequest();
            $leaveRequest->reference_number = LeaveRequest::generateUniqueReferenceNumber($business->id);
            $leaveRequest->employee_id      = $employeeId;
            $leaveRequest->business_id      = $business->id;
            $leaveRequest->leave_type_id    = $validatedData['leave_type_id'];
            $leaveRequest->start_date       = $startDate;
            $leaveRequest->end_date         = $endDate;
            $leaveRequest->total_days       = $totalDays;
            $leaveRequest->reason           = $validatedData['reason'] ?? null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('attachments', 'public');
                $leaveRequest->attachment = $path;
            }
            $leaveRequest->save();

            // Auto-approval if not requiring approval
            if (!$leaveType->requires_approval) {
                $leaveRequest->approved_by = auth()->id();
                $leaveRequest->approved_at = now();
                $leaveRequest->save();
            }

            // Check if HR email exists before sending
            $recipient = $business->hr_email ?? null;

            if ($recipient) {
                \Log::info('Sending leave request email to: ' . $recipient);
                Mail::to($recipient)->queue(new LeaveRequestSubmitted($leaveRequest));
            } else {
                \Log::warning("Leave request {$leaveRequest->id} submitted but no HR email found for business ID {$business->id}");
            }

            return RequestResponse::ok('Leave request created successfully.');
        });
    }

    public function status(Request $request)
    {
        $validatedData = $request->validate([
            'reference_number'  => 'required|exists:leave_requests,reference_number',
            'status'            => 'required|in:approved,rejected',
            'rejection_reason'  => 'nullable|required_if:status,rejected|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $leaveRequest = LeaveRequest::where('reference_number', $validatedData['reference_number'])->firstOrFail();

            if ($validatedData['status'] === 'approved') {
                $leaveRequest->approved_by = auth()->id();
                $leaveRequest->approved_at = now();
                $leaveRequest->rejection_reason = null;
            } else {
                $leaveRequest->approved_by = null;
                $leaveRequest->approved_at = null;
                $leaveRequest->rejection_reason = $validatedData['rejection_reason'];
            }

            $leaveRequest->save();

            $leaveRequest->employee->user->notify(new LeaveStatusNotification($leaveRequest));

            return RequestResponse::ok("Leave request {$validatedData['status']} successfully.");
        });
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
