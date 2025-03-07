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

class LeaveRequestController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $leaveRequests = LeaveRequest::where('business_id', $business->id);
        $status = $request->status;

        if ($request->has('status')) {
            switch ($status) {
                case 'pending':
                    $leaveRequests->whereNull('approved_by');
                    break;
                case 'approved':
                    $leaveRequests->whereNotNull('approved_by');
                    break;
                case 'rejected':
                    $leaveRequests->whereNotNull('rejection_reason');
                    break;
            }
        }

        $leaveRequests = $leaveRequests->with('employee', 'leaveType')->latest()->paginate(10);

        $leaveRequestsTable = view('leave._leave_requests_table', compact('leaveRequests', 'status'))->render();
        return RequestResponse::ok('Leave requests fetched successfully.', $leaveRequestsTable);
    }

    public function show(Request $request, $referenceNumber)
    {
        $leaveRequest = LeaveRequest::where('reference_number', $referenceNumber)->with('employee', 'leaveType')->firstOrFail();
        $leaveRequestDetails = view('leave._leave_request_table', compact('leaveRequest'))->render();
        return RequestResponse::ok('Leave request fetched successfully.', $leaveRequestDetails);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            $leave_type = LeaveType::findOrFail($validatedData['leave_type_id']);
            $duration = $leave_type->max_continuous_days;

            $employeeId = auth()->user()->employee->id;
            $startDate = $validatedData['start_date'];
            $endDate = date('Y-m-d', strtotime("$startDate +$duration days"));

            $existingLeave = LeaveRequest::where('employee_id', $employeeId)
                ->whereHas('statuses', function ($query) {
                    $query->whereNotIn('name', ['rejected', 'used_up']);
                })
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate]) // Existing leave starts within the new leave period
                        ->orWhereBetween('end_date', [$startDate, $endDate]) // Existing leave ends within the new leave period
                        ->orWhere(function ($query) use ($startDate, $endDate) {
                            $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate); // New leave is fully within an existing leave
                        });
                })
                ->exists();

            if ($existingLeave) {
                return RequestResponse::badRequest('You already have a leave request that overlaps with these dates.');
            }

            // Create new leave request
            $leaveRequest = new LeaveRequest();
            $leaveRequest->reference_number = LeaveRequest::generateUniqueReferenceNumber($business->id);
            $leaveRequest->employee_id = $employeeId;
            $leaveRequest->business_id = $business->id;
            $leaveRequest->leave_type_id = $validatedData['leave_type_id'];
            $leaveRequest->start_date = $startDate;
            $leaveRequest->total_days = $duration;
            $leaveRequest->reason = $validatedData['reason'];
            $leaveRequest->end_date = $endDate;
            $leaveRequest->save();

            $leaveRequest->setStatus(Status::PENDING);

            return RequestResponse::created('Leave request created successfully.');
        });
    }


    public function status(Request $request)
    {
        $validatedData = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|required_if:action,reject|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $leaveRequest = LeaveRequest::where('reference_number', $validatedData['reference_number'])->firstOrFail();

            if ($validatedData['status'] === 'approved') {
                $leaveRequest->approved_by = auth()->id();
                $leaveRequest->approved_at = now();
                $leaveRequest->setStatus(Status::APPROVED);
                $leaveRequest->setStatus(Status::ACTIVE);
            } else {
                $rejection_reason = $validatedData['rejection_reason'];
                $leaveRequest->setStatus(Status::DECLINED, $rejection_reason);
            }

            $leaveRequest->save();

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
