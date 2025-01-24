<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Models\LeaveEntitlement;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;

class LeaveEntitlementController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $leavePeriods = $business->leavePeriods()->get();
        $leavePeriodTable = view('leave._leave_periods_table', compact('leavePeriods'))->render();
        return RequestResponse::ok('Leave periods fetched successfully.', $leavePeriodTable);
    }
    function store(Request $request)
    {
        Log::debug($request->all());
        $validatedData = $request->validate([
            'leave_period_id' => 'required|exists:leave_periods,id',
            'department' => 'nullable|string',
            'job_category' => 'nullable|string',
            'employment_term' => 'nullable|string',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'nullable|exists:employees,id',
            'leave_type_ids' => 'required|array',
            'leave_type_ids.*' => 'required|exists:leave_types,id',
            'entitled_days' => 'required|array',
            'entitled_days.*' => 'required|numeric|min:0',
            'total_days' => 'required|array',
            'total_days.*' => 'required|numeric|min:0',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));

            return RequestResponse::created('Leave period created successfully.');
        });
    }
}
