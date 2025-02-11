<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\LeavePeriod;
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
        $validatedData = $request->validate([
            'leave_period_slug' => 'required|exists:leave_periods,slug',
        ]);
        $business = Business::findBySlug(session('active_business_slug'));

        if (!$business) {
            return RequestResponse::badRequest('Business not found.', 404);
        }

        $leavePeriod = LeavePeriod::where('slug', $validatedData['leave_period_slug'])->where('business_id', $business->id)->first();

        if (!$leavePeriod) {
            return RequestResponse::badRequest('Leave period not found.', 404);
        }


        $leaveEntitlementsQuery = LeaveEntitlement::where('business_id', $business->id)
            ->where('leave_period_id', $leavePeriod->id);

        if ($request->has('location_id')) {
            $leaveEntitlementsQuery->whereHas('employee', function ($query) use ($request) {
                $query->where('location_id', $request->location_id);
            });
        } else {
            $leaveEntitlementsQuery->whereHas('employee', function ($query) {
                $query->whereNull('location_id');
            });
        }


        $leaveEntitlements = $leaveEntitlementsQuery->with(['employee', 'leaveType', 'leavePeriod'])->get();

        $leaveEntitlementsTable = view('leave._leave_entitlements_table', compact('leaveEntitlements'))->render();
        return RequestResponse::ok('Leave entitlements fetched successfully.', $leaveEntitlementsTable);
    }

    public function store(Request $request)
    {
        Log::debug($request->all());

        $validatedData = $request->validate([
            'leave_period_id' => 'required|exists:leave_periods,id',
            'employees' => 'required|array',
            'employees.*' => 'nullable|exists:employees,id',
            'leave_type_ids' => 'required|array',
            'leave_type_ids.*' => 'required|exists:leave_types,id',
            'entitled_days' => 'required|array',
            'entitled_days.*' => 'required|numeric|min:0',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));

            if (!$business) {
                return RequestResponse::badRequest('Business not found.', 404);
            }

            $leaveEntitlements = [];
            foreach ($validatedData['employees'] as $employeeId) {
                foreach ($validatedData['leave_type_ids'] as $index => $leaveTypeId) {
                    $entitledDays = $validatedData['entitled_days'][$index] ?? 0;

                    $existingEntitlement = LeaveEntitlement::where([
                        'business_id' => $business->id,
                        'employee_id' => $employeeId,
                        'leave_type_id' => $leaveTypeId,
                        'leave_period_id' => $validatedData['leave_period_id'],
                    ])->first();

                    if ($existingEntitlement) {
                        // If entitlement exists, update it
                        $existingEntitlement->update([
                            'entitled_days' => $entitledDays,
                            'total_days' => $entitledDays,
                            'days_remaining' => $entitledDays - $existingEntitlement->days_taken,
                        ]);
                    } else {
                        // If new, create entitlement
                        $leaveEntitlements[] = [
                            'business_id' => $business->id,
                            'employee_id' => $employeeId,
                            'leave_type_id' => $leaveTypeId,
                            'leave_period_id' => $validatedData['leave_period_id'],
                            'entitled_days' => $entitledDays,
                            'total_days' => $entitledDays,
                            'days_taken' => 0,
                            'days_remaining' => $entitledDays,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            if (!empty($leaveEntitlements)) {
                LeaveEntitlement::insert($leaveEntitlements);
            }

            return RequestResponse::created('Leave entitlements assigned successfully.');
        });
    }

}
