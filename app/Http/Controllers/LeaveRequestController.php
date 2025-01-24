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
        $leaveRequestDetails = view('leave._leave_request_details', compact('leaveRequest'))->render();
        return RequestResponse::ok('Leave request fetched successfully.', $leaveRequestDetails);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'half_day' => 'boolean',
            'half_day_type' => 'nullable|in:first_half,second_half',
            'reason' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));

            $leaveRequest = new LeaveRequest();
            $leaveRequest->reference_number = LeaveRequest::generateUniqueReferenceNumber($business->id);
            $leaveRequest->employee_id = auth()->user()->employee->id;
            $leaveRequest->business_id = $business->id;
            $leaveRequest->leave_type_id = $validatedData['leave_type_id'];
            $leaveRequest->start_date = $validatedData['start_date'];
            $leaveRequest->end_date = $validatedData['end_date'];
            $leaveRequest->total_days = $this->calculateTotalDays($validatedData['start_date'], $validatedData['end_date'], $validatedData['half_day']);
            $leaveRequest->half_day = $validatedData['half_day'];
            $leaveRequest->half_day_type = $validatedData['half_day_type'];
            $leaveRequest->reason = $validatedData['reason'];
            $leaveRequest->save();

            $leaveRequest->setStatus(Status::PENDING);

            return RequestResponse::created('Leave request created successfully.');
        });
    }

    public function approve(Request $request, $referenceNumber)
    {
        $validatedData = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $leaveRequest = LeaveRequest::where('reference_number', $validatedData['reference_number'])->firstOrFail();
            $leaveRequest->approved_by = auth()->id();
            $leaveRequest->approved_at = now();
            $leaveRequest->save();

            $leaveRequest->setStatus(Status::APPROVED);

            return RequestResponse::ok('Leave request approved successfully.');
        });
    }

    public function reject(Request $request, $referenceNumber)
    {
        $validatedData = $request->validate([
            'reference_number' => 'required|exists:leave_requests,reference_number',
            'rejection_reason' => 'required|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $leaveRequest = LeaveRequest::where('reference_number', $validatedData['reference_number'])->firstOrFail();
            $leaveRequest->rejection_reason = $validatedData['rejection_reason'];
            $leaveRequest->save();
            $leaveRequest->setStatus(Status::DECLINED);

            return RequestResponse::ok('Leave request rejected successfully.');
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
        $days = $endDate->diffInDays($startDate) + 1; // Include both start and end dates

        if ($halfDay) {
            $days -= 0.5;
        }

        return $days;
    }
}
