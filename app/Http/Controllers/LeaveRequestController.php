<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Business;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Carbon\Carbon;

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

        // ✅ Restrict non-HR/admin users to their own leaves
        if (!in_array($activeRole, ['business-hr', 'business-admin'])) {
            if ($user->employee) {
                $leaveRequests->where('employee_id', $user->employee->id);
            } else {
                $leaveRequests->whereRaw('1=0'); // no results
            }
        }

        // ✅ Status filtering based on Blade logic
        if ($request->has('status')) {
            switch ($status) {
                case 'pending':
                    $leaveRequests->whereNull('approved_by')
                                ->whereNull('rejection_reason');
                    break;

                case 'approved':
                    $leaveRequests->whereNotNull('approved_by')
                                ->whereNull('rejection_reason');
                    break;

                case 'rejected':
                    $leaveRequests->whereNotNull('rejection_reason')
                                ->whereNull('approved_by');
                    break;
            }
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

        // ✅ Validation rules
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

            // ✅ Check max continuous days limit
            if ($leaveType->max_continuous_days && $totalDays > $leaveType->max_continuous_days) {
                return RequestResponse::badRequest("You cannot take more than {$leaveType->max_continuous_days} days for this leave type.");
            }

            // ✅ Prevent overlapping leave requests
            $existingLeave = LeaveRequest::where('employee_id', $employeeId)
                ->whereHas('statuses', function ($query) {
                    $query->whereNotIn('name', ['rejected', 'used_up']);
                })
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($query) use ($startDate, $endDate) {
                            $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })
                ->exists();

            if ($existingLeave) {
                return RequestResponse::badRequest('You already have a leave request that overlaps with these dates.');
            }

            // ✅ Create leave request
            $leaveRequest = new LeaveRequest();
            $leaveRequest->reference_number = LeaveRequest::generateUniqueReferenceNumber($business->id);
            $leaveRequest->employee_id      = $employeeId;
            $leaveRequest->business_id      = $business->id;
            $leaveRequest->leave_type_id    = $validatedData['leave_type_id'];
            $leaveRequest->start_date       = $startDate;
            $leaveRequest->end_date         = $endDate;
            $leaveRequest->total_days       = $totalDays;
            $leaveRequest->reason           = $validatedData['reason'] ?? null;

            // ✅ Handle attachment
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('attachments', 'public');
                $leaveRequest->attachment = $path;
            }

            $leaveRequest->save();
            $leaveRequest->setStatus(Status::PENDING);

            //Queue notification to HR/admin for approval (if required)
            if ($leaveType->requires_approval) {
                // Notification logic here (e.g., dispatch a job)
            } else {
                // Auto-approve logic if no approval is required
                $leaveRequest->approved_by = auth()->id();
                $leaveRequest->approved_at = now();
                $leaveRequest->setStatus(Status::APPROVED);
                $leaveRequest->save();
            }           

            //Queue email to HR/admin for approval (if required)
            Mail::to($business->hr_email)->queue(new LeaveRequestSubmitted($leaveRequest));

            return RequestResponse::created('Leave request created successfully.');
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

                // Set only APPROVED status (remove redundant ACTIVE)
                $leaveRequest->setStatus(Status::APPROVED);
            } else {
                $leaveRequest->approved_by = null;
                $leaveRequest->approved_at = null;
                $leaveRequest->rejection_reason = $validatedData['rejection_reason'];

                $leaveRequest->setStatus(Status::DECLINED);
            }

            $leaveRequest->save();
            // Notify employee about status change
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

    private function calculateTotalDays($startDate, $endDate, $halfDay)
    {
        $days = $endDate->diffInDays($startDate) + 1;
        if ($halfDay) {
            $days -= 0.5;
        }
        return $days;
    }
}
