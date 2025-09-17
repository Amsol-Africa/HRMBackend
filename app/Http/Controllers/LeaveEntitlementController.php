<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\LeavePeriod;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Models\LeaveEntitlement;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;

class LeaveEntitlementController extends Controller
{
    use HandleTransactions;

    /**
     * Create or update leave entitlements for one or many employees over one or many leave types.
     * - If "employees" is omitted, all employees in the active business are targeted.
     * - "leave_type_ids" and "entitled_days" are parallel arrays (index-aligned).
     */
    public function store(Request $request)
    {
        Log::debug('LeaveEntitlement store payload', $request->all());

        $validated = $request->validate([
            'leave_period_id'           => 'required|exists:leave_periods,id',

            // Optional explicit list of employees (defaults to all in business)
            'employees'                 => 'nullable|array',
            'employees.*'               => 'nullable|integer|exists:employees,id',

            // Parallel arrays (index aligned)
            'leave_type_ids'            => 'required|array|min:1',
            'leave_type_ids.*'          => 'required|integer|exists:leave_types,id',

            'entitled_days'             => 'required|array|min:1',
            'entitled_days.*'           => 'required|numeric|min:0',
        ]);

        return $this->handleTransaction(function () use ($validated) {
            $business = Business::findBySlug(session('active_business_slug'));

            if (!$business) {
                return RequestResponse::badRequest('Business not found.', 404);
            }

            $leavePeriod = LeavePeriod::where('id', $validated['leave_period_id'])
                ->where('business_id', $business->id)
                ->first();

            if (!$leavePeriod) {
                return RequestResponse::badRequest('Leave period not found.', 404);
            }

            // If employees list is missing, target ALL employees in the business
            $employeeIds = $validated['employees'] ?? Employee::where('business_id', $business->id)->pluck('id')->toArray();

            // Guard: ensure the arrays are aligned
            $typeIds = $validated['leave_type_ids'];
            $daysArr = $validated['entitled_days'];
            if (count($typeIds) !== count($daysArr)) {
                return RequestResponse::badRequest('leave_type_ids and entitled_days must be the same length.', 422);
            }

            $now = now();
            $bulkInsert = [];

            foreach ($employeeIds as $employeeId) {
                foreach ($typeIds as $idx => $leaveTypeId) {
                    $entitledDays = (float)($daysArr[$idx] ?? 0);

                    // Check existing entitlement for same key (unique per employee/type/period)
                    $existing = LeaveEntitlement::where([
                        'business_id'    => $business->id,
                        'employee_id'    => $employeeId,
                        'leave_type_id'  => $leaveTypeId,
                        'leave_period_id'=> $leavePeriod->id,
                    ])->first();

                    if ($existing) {
                        // Update: entitled/total reset to provided; days_remaining recomputed against days_taken
                        $existing->update([
                            'entitled_days'   => $entitledDays,
                            'total_days'      => $entitledDays + (float)($existing->accrued_days ?? 0),
                        ]);
                        // recompute remaining using model method
                        $existing->calculateRemainingDays();
                    } else {
                        $bulkInsert[] = [
                            'business_id'     => $business->id,
                            'employee_id'     => $employeeId,
                            'leave_type_id'   => $leaveTypeId,
                            'leave_period_id' => $leavePeriod->id,
                            'entitled_days'   => $entitledDays,
                            'accrued_days'    => 0,              // start at 0; accrual can add later
                            'total_days'      => $entitledDays,  // entitled + accrued
                            'days_taken'      => 0,
                            'days_remaining'  => $entitledDays,  // nothing used yet
                            'created_at'      => $now,
                            'updated_at'      => $now,
                        ];
                    }
                }
            }

            if (!empty($bulkInsert)) {
                LeaveEntitlement::insert($bulkInsert);
            }

            return RequestResponse::created('Leave entitlements assigned successfully.', [
                'leave_period_slug' => $leavePeriod->slug, // Return slug for frontend consistency
            ]);
        });
    }

    /**
     * Fetch entitlements table for a given leave period (scoped to active business).
     * Optional filter: location_id. If omitted, no location filter is applied.
     */
    public function fetch(Request $request)
    {
        $validated = $request->validate([
            'leave_period_slug' => 'required|exists:leave_periods,slug',
            'location_id'       => 'nullable|integer|exists:locations,id',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.', 404);
        }

        $leavePeriod = LeavePeriod::where('slug', $validated['leave_period_slug'])
            ->where('business_id', $business->id)
            ->first();

        if (!$leavePeriod) {
            return RequestResponse::badRequest('Leave period not found.', 404);
        }

        $query = LeaveEntitlement::where('business_id', $business->id)
            ->where('leave_period_id', $leavePeriod->id);

        // Optional location filter (only apply if provided)
        if (!empty($validated['location_id'])) {
            $locationId = (int)$validated['location_id'];
            $query->whereHas('employee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }

        // Eager-load nested user to avoid N+1 in the blade
        $leaveEntitlements = $query->with(['employee.user', 'leaveType', 'leavePeriod'])->get();

        $leaveEntitlementsTable = view('leave._leave_entitlements_table', compact('leaveEntitlements'))->render();

        return RequestResponse::ok('Leave entitlements fetched successfully.', $leaveEntitlementsTable);
    }
}
